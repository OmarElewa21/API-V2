<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\User\CreateAdminRequest;
use App\Http\Requests\User\UpdateAdminRequest;


class AdminsController extends Controller
{
    /**
     * Display a listing of the admins.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(User::admins()->with('role', 'role.permission')->get(), 200);
    }

    /**
     * Store a newly created resource in admins.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAdminRequest $request)
    {
        if(User::withTrashed()->where('email', $request->email)->exists()){
            $user = User::withTrashed()->where('email', $request->email)->first();
            
            if(User::withTrashed()->whereNot('id', $user->id)->where('username', $request->username)->exists()){
                // check if request username already exists and is not for the same user
                return response()->json(['username' => ['username aleardy exists']], 422);
            }
            $user->deleted_at = null;
        }else{
            $user = new User;
        }
        $user->fill([
            'name'          => $request->name,
            'username'      => $request->username,
            'email'         => $request->email,
            'role_id'       => Role::where('name', $request->role)->value('id'),
            'password'      => bcrypt($request->password),
        ])->save();
        return response($user, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response($user->load('role', 'role.permission'), 200);
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
            'role_id'       => Role::where('name', $request->role)->value('id'),
            'password'      => bcrypt($request->password),
        ]);
        return response($user->load('role', 'role.permission'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->index();
    }
}
