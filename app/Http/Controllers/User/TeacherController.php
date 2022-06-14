<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\User\CreateTeacherRequest;
use App\Http\Requests\User\UpdateTeacherRequest;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    /**
     * Display a listing of the teachers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(Teacher::get(), 200);
    }

    /**
     * Store a newly created resource in teachers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTeacherRequest $request)
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
                if(Teacher::withTrashed()->where('user_id', User::where('username', $data['username'])->value('id'))->exists()){
                    $teacher = Teacher::withTrashed()->where('user_id', User::where('username', $data['username'])->value('id'))->firstOrFail();
                    $teacher->update(
                        [
                            'country_partner_id'    => $data['country_partner_id'],
                            'school_id'             => $data['school_id'],
                            'country_id'            => $data['country_id'],
                            'deleted_at'            => null
                        ]
                    );
                }else{
                    Teacher::create(
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
     * Display the specified teachers.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function show(Teacher $teacher)
    {
        return response($teacher, 200);
    }

    /**
     * Update the specified resource in teachers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $teacher->user->update([
            'name'          => $request->name,
            'username'      => $request->username,
            'email'         => $request->email,
            'role_id'       => Role::where('name', $request->role)->value('id'),
            'password'      => bcrypt($request->password),
            'updated_by'    => auth()->id()
        ]);
        $teacher->update([
            'country_partner_id'    => $request->country_partner_id,
            'school_id'             => $request->school_id,
            'country_id'            => $request->country_id,
        ]);
        return response($teacher, 200);
    }

    /**
     * Remove the specified resource from teachers.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return $this->index();
    }
}
