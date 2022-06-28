<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordReset;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    private $secret_key = "8A0B0DC579F3DA6A417409BFB9071FDA663457F347B735D621FC9B3AA90796D3";

    public function login(Request $request)
    {
        // Request Validation
        $request->validate([
            'identifier' => 'required',
            'password' => 'required'
        ]);

        if(filter_var($request->identifier, FILTER_VALIDATE_EMAIL)){
            $identifier = 'email';
        }else{
            $identifier = 'username';
        }

        // Check if user exists
        if( User::where($identifier, $request->identifier)->doesntExist() ){
            return response()->json(["message" => "User not found"], 404);
        }

        // Auth attempt for given credintials
        if(!auth()->attempt([$identifier => $request->identifier, 'password' => $request->password])){
            return response()->json(["message" => "Invalid credentials"], 422);
        }

        $user = User::where($identifier, $request->identifier)->with(['role:id,name,uuid'])->firstOrFail();      // get user
        $authToken = $user->createToken("auth_token")->plainTextToken;                                        // generate token

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

    public function sendResetLink(Request $request){
        if(filter_var($request->identifier, FILTER_VALIDATE_EMAIL)){
            $identifier = 'email';
        }else{
            $identifier = 'username';
        }

        if($this->secret_key !== $request->secret_key){
            return response()->json(['message' => 'Unauthorized Request'], 401);
        }
        if(User::where($identifier, $request->identifier)->doesntExist()){
            return response()->json(['message' => "Username doesn't exists"], 404); 
        }else{
            $user = User::where($identifier, $request->identifier)->first();
        }

        if(PasswordReset::where('username', $request->username)->exists()){
            PasswordReset::where('username', $request->username)->delete();
        }

        $user_key = Str::random(10);
        PasswordReset::create([
            'username'      => $user->username,
            'user_key'      => $user_key,
            'created_at'    => now()->toDateTimeString()
        ]);

        $link = 'https://simcc.org/reset_password?username='. 
                    $user->username . '&' . 'user_key=' . $user_key;

        try {
            Mail::to($user)->send(new ResetPassword($link));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Mail sent successfully'], 200);
    }

    public function changePassword(ChangePasswordRequest $request){
        $user = User::where('username', $request->username)->firstOrFail();
        if(PasswordReset::where('username', $request->username)->value('user_key') !== $request->user_key){
            return response()->json(['message' => 'user key and username doesnnot match'], 401);
        }
        $user->update([
            'password'  => bcrypt($request->password),
        ]);
        PasswordReset::where('username', $request->username)->delete();
        return response()->json(['message' => 'Password changed successfully'], 200);
    }

    /************************************ Index Section *************************************************/
    protected function indexForSuperAdmin($data){
        return $data->whereRelation('role', 'name', '<>', 'super admin')
                ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
                ->leftJoin('countries', 'countries.id', '=', 'users.country_id')
                ->leftJoin('personal_access_tokens as pst', function ($join) {
                    $join->on('users.id', '=', 'pst.tokenable_id')
                        ->where('pst.tokenable_type', 'App\Models\User');
                })
                ->select('users.*', 'roles.name as role', 'countries.name as country', 'pst.updated_at as last_login');
    }

    protected function indexForAdmin($data){
        return $data->whereRelation('role', 'name', '<>', 'super admin')
                ->whereRelation('role', 'name', '<>', 'admin')
                ->join('roles as r', 'r.id', '=', 'users.role_id')
                ->leftJoin('personal_access_tokens as pst', function ($join) {
                    $join->on('users.id', '=', 'pst.tokenable_id')
                        ->where('pst.tokenable_type', 'App\Models\User');
                })
                ->select('users.*', 'r.name as role', 'pst.updated_at as last_login');
    }

    protected function indexForCountryPartner($data){
        return User::whereRelation('role', 'name', '=', 'country parnter assistant')
                ->orWhereRelation('role', 'name', '=', 'school manager')
                ->orWhereRelation('role', 'name', '=', 'teacher')
                ->join('roles as r', 'r.id', '=', 'users.role_id')
                ->leftJoin('personal_access_tokens as pst', function ($join) {
                    $join->on('users.id', '=', 'pst.tokenable_id')
                        ->where('pst.tokenable_type', 'App\Models\User');
                })
                ->select('users.*', 'r.name as role', 'pst.updated_at as last_login');
    }

     /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->has('filterOptions')){
            $request->validate([
                'filterOptions'                 => 'array',
                'filterOptions.role'            => 'exists:roles,name',
                'filterOptions.country'         => 'exists:countries,id',
                'filterOptions.status'          => ['string', Rule::in(['enabled', 'disabled', 'deleted'])]
            ]);
            $data = User::applyFilter($request->get('filterOptions'));
        }else{
            $data = new User;
        }
        $filterOptions = User::getFilterForFrontEnd($data);
        
        switch (auth()->user()->role->name) {
            case 'super admin':
                $users = $this->indexForSuperAdmin($data);
                break;
            case 'admin':
                $users = $this->indexForAdmin($data);
                break;
            case 'country partner':
                $users = $this->indexForCountryPartner($data);
                break;
            default:
                # code...
                break;
        }
        return response($filterOptions->merge($users
            ->paginate(is_numeric($request->paginationNumber) ? $request->paginationNumber : 5)
            )->forget(['links', 'first_page_url', 'last_page_url', 'next_page_url', 'path', 'prev_page_url'])
            ,200);
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
