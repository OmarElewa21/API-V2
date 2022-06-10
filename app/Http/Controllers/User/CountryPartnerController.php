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
                            'deleted_at'    => null
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
                        ]
                    );
                }
                if(CountryPartner::withTrashed()->where('user_id', User::where('username', $data['username'])->value('id'))->exists()){
                    $countryPartner = CountryPartner::withTrashed()->where('user_id', User::where('username', $data['username'])->value('id'))->firstOrFail();
                    $countryPartner->update(
                        [
                            'organization_id'   => Organization::where('name', $data['organization'])->value('id'),
                            'country_id'        => $data['country_id'],
                            'deleted_at'        => null
                        ]
                    );
                }else{
                    CountryPartner::create(
                        [
                            'user_id'           => User::where('username', $data['username'])->value('id'),
                            'organization_id'   => Organization::where('name', $data['organization'])->value('id'),
                            'country_id'        => $data['country_id'],
                            'deleted_at'        => null
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
