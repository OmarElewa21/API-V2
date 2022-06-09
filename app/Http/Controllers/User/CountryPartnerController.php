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

class CountryPartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(CountryPartner::get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCountryPartnerRequest $request)
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
        $countryPartner = new CountryPartner;
        try {
            $user->fill([
                'name'          => $request->name,
                'username'      => $request->username,
                'email'         => $request->email,
                'role_id'       => Role::where('name', $request->role)->value('id'),
                'password'      => bcrypt($request->password),
            ])->save();
            $countryPartner->fill([
                'user_id'           => $user->id,
                'organization_id'   => Organization::where('name', $request->organization)->value('id'),
                'country_id'        => $request->country_id
            ])->save();
            return response($countryPartner->load('user'), 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CountryPartner  $countryPartner
     * @return \Illuminate\Http\Response
     */
    public function show(CountryPartner $countryPartner)
    {
        return response($countryPartner, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CountryPartner  $countryPartner
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCountryPartnerRequest $request, CountryPartner $countryPartner)
    {
        $countryPartner->user->update([
            'name'          => $request->name,
            'username'      => $request->username,
            'email'         => $request->email,
            'role_id'       => Role::where('name', $request->role)->value('id'),
            'password'      => bcrypt($request->password),
        ]);
        $countryPartner->update([
            'organization_id'   => Organization::where('name', $request->organization)->value('id'),
            'country_id'        => $request->country_id
        ]);
        return response($countryPartner, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CountryPartner  $countryPartner
     * @return \Illuminate\Http\Response
     */
    public function destroy(CountryPartner $countryPartner)
    {
        $countryPartner->delete();
        return $this->index();
    }
}
