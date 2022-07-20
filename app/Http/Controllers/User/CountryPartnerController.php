<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use App\Models\CountryPartner;
use App\Http\Requests\User\CreateCountryPartnerRequest;
use App\Http\Requests\User\UpdateCountryPartnerRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CountryPartnerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCountryPartnerRequest $request)
    {
        $collection = new Collection;
        DB::beginTransaction();
        foreach($request->all() as $key=>$data){
            try {
                if(User::withTrashed()->where('username', $data['username'])->orWhere('email', $data['email'])->exists()){
                    $user = User::withTrashed()->where('username', $data['username'])->orWhere('email', $data['email'])->firstOrFail();
                    $user->update(
                        [
                            'username'      => $data['username'],
                            'email'         => $data['email'],
                            'name'          => $data['name'],
                            'password'      => bcrypt($data['password']),
                            'deleted_at'    => null,
                            'deleted_by'    => null,
                            'updated_by'    => auth()->id(),
                            'status'        => 'Enabled'
                        ]
                    );
                }else{
                    User::Create(
                        [
                            'username'      => $data['username'],
                            'email'         => $data['email'],
                            'name'          => $data['name'],
                            'role_id'       => Role::where('name', $data['role'])->value('id'),
                            'password'      => bcrypt($data['password']),
                            'created_by'    => auth()->id(),
                            'country_id'    => $data['country_id']
                        ]
                    );
                    CountryPartner::create(
                        [
                            'user_id'           => User::where('username', $data['username'])->value('id'),
                            'organization_id'   => Organization::where('name', $data['organization'])->value('id')
                        ]
                    );
                }
                $collection->push(
                    User::where('username', $data['username'])
                    ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
                    ->leftJoin('countries', 'countries.id', '=', 'users.country_id')
                    ->with('personal_access:tokenable_id,last_used_at as last_login')
                    ->select('users.*', 'roles.name as role', 'countries.name as country')
                    ->first());
            } catch (Exception $e) {
                DB::rollBack();
                return response($e->getMessage(), 500);
            }
        }
        DB::commit();
        return $collection;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user = User::withTrashed()->where('username', $user->username)
                    ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
                    ->leftJoin('countries', 'countries.id', '=', 'users.country_id')
                    ->with('personal_access:tokenable_id,last_used_at as last_login')
                    ->select('users.*', 'roles.name as role', 'countries.name as country')
                    ->firstOrFail();
        return response($user, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCountryPartnerRequest $request, User $user)
    {
        $user->update([
            'name'          => $request->name,
            'username'      => $request->username,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'updated_by'    => auth()->id()
        ]);
        return $this->show($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->update([
            'status'     => 'Deleted',
            'deleted_by' => auth()->id()
        ]);
        $user->delete();
        return $this->show($user);
    }
}
