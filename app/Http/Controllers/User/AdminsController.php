<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\User\CreateAdminRequest;
use App\Http\Requests\User\UpdateAdminRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AdminsController extends Controller
{
    /**
     * Store a newly created resource in admins.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAdminRequest $request)
    {
        DB::beginTransaction();
        $collection = new Collection;
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
                            'updated_by'    => auth()->id(),
                            'deleted_at'    => null,
                            'deleted_by'    => null
                        ]
                    );
                }else{
                    User::create(
                        [
                            'username'      => $data['username'],
                            'email'         => $data['email'],
                            'name'          => $data['name'],
                            'role_id'       => Role::where('name', 'admin')->value('id'),
                            'password'      => bcrypt($data['password']),
                            'created_by'    => auth()->id()
                        ]
                    );
                }
                $collection->push(
                    User::where('username', $data['username'])
                    ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
                    ->leftJoin('countries', 'countries.id', '=', 'users.country_id')
                    ->leftJoin('personal_access_tokens as pst', function ($join) {
                            $join->on('users.id', '=', 'pst.tokenable_id')
                                ->where('pst.tokenable_type', 'App\Models\User');
                            })
                    ->select('users.*', 'roles.name as role', 'countries.name as country', 'pst.updated_at as last_login')
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
     * Display the specified user.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user = User::withTrashed()->where('username', $user->username)
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.*', 'roles.name as role')->firstOrFail();
        return response($user, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdminRequest $request, User $user)
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
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->update([
            'status'     => 'deleted',
            'deleted_by' => auth()->id()
        ]);
        $user->delete();
        return $this->show($user);
    }
}
