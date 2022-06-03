<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;


class UsersController extends Controller
{
    public function login(Request $request)
    {
        // Request Validation
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // Check if there is a record with given username
        if( User::where("username", $request->username)->doesntExist() ){
            return response()->json(["message" => "User not found"], 401);
        }

        // Auth attempt for given credintials
        if(!auth()->attempt(request(['username', 'password']))){
            return response()->json(["message" => "Invalid credintials"], 422);
        }
        
        $user = User::where("username", $request->username)->with(['role', 'role.permission'])->first();      // get user
        $user->tokens()->delete();                                                          // delete any tokens
        $authToken = $user->createToken("auth_token")->plainTextToken;                      // generate token

        return response()->json([
            "user"    =>   $user,
            "token"   =>   $authToken
        ], 200);
    }

    public function logout()
    {
        if(!auth()->check()){
            return response()->json(["message" => "No User Authinticated"], 400);
        }
        auth()->user()->tokens()->delete();
        return response()->json(["message" => "User logged out successfully"], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
