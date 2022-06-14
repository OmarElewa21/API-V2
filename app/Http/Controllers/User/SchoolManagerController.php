<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SchoolManager;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\User\CreateSchoolManagerRequest;
use App\Http\Requests\User\UpdateSchoolManagerRequest;
use Illuminate\Support\Facades\DB;

class SchoolManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(SchoolManager::get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSchoolManagerRequest $request)
    {
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
                            'updated_by'    => auth()->id()
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
                }
                if(SchoolManager::withTrashed()->where('user_id', User::where('username', $data['username'])->value('id'))->exists()){
                    $schoolManager = SchoolManager::withTrashed()->where('user_id', User::where('username', $data['username'])->value('id'))->firstOrFail();
                    $schoolManager->update(
                        [
                            'country_partner_id'    => $data['country_partner_id'],
                            'school_id'             => $data['school_id'],
                            'country_id'            => $data['country_id'],
                            'deleted_at'            => null
                        ]
                    );
                }else{
                    SchoolManager::create(
                        [
                            'user_id'               => User::where('username', $data['username'])->value('id'),
                            'country_partner_id'    => $data['country_partner_id'],
                            'school_id'             => $data['school_id'],
                            'country_id'            => $data['country_id'],
                        ]
                    );
                }
                
            } catch (Exception $e) {
                DB::rollBack();
                return response($e->getMessage(), 500);
            }
        }
        DB::commit();
        return $this->index();
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
