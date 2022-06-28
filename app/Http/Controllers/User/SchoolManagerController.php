<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SchoolManager;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\User\CreateSchoolManagerRequest;
use App\Http\Requests\User\UpdateSchoolManagerRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class SchoolManagerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSchoolManagerRequest $request)
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
                            'role_id'       => Role::where('name', $data['role'])->value('id'),
                            'password'      => bcrypt($data['password']),
                            'deleted_at'    => null,
                            'deleted_by'    => null,
                            'updated_by'    => auth()->id(),
                            'status'        => 'enabled'
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
                            'created_by'    => auth()->id()
                        ]
                    );
                    SchoolManager::create(
                        [
                            'user_id'               => User::where('username', $data['username'])->value('id'),
                            'country_partner_id'    => $data['country_partner_id'],
                            'school_id'             => $data['school_id'],
                        ]);
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
     * Display the specified resource.
     *
     * @param  \App\Models\SchoolManager  $schoolManager
     * @return \Illuminate\Http\Response
     */
    public function show(SchoolManager $schoolManager)
    {
        return response($schoolManager, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SchoolManager  $schoolManager
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSchoolManagerRequest $request, SchoolManager $schoolManager)
    {
        $schoolManager->user->update([
            'name'          => $request->name,
            'username'      => $request->username,
            'email'         => $request->email,
            'role_id'       => Role::where('name', $request->role)->value('id'),
            'password'      => bcrypt($request->password),
            'updated_by'    => auth()->id()
        ]);
        $schoolManager->update([
            'country_partner_id'    => $request->country_partner_id,
            'school_id'             => $request->school_id,
            'country_id'            => $request->country_id,
        ]);
        return response($schoolManager, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SchoolManager  $schoolManager
     * @return \Illuminate\Http\Response
     */
    public function destroy(SchoolManager $schoolManager)
    {
        $schoolManager->delete();
        return $this->index();
    }
}
