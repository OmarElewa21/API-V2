<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CountryPartnerAssistant;
use App\Models\CountryPartner;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\User\CreateCountryPartnerAssistantRequest;
use App\Http\Requests\User\UpdateCountryPartnerAssistantRequest;
use Illuminate\Support\Facades\DB;

class CountryPartnerAssistantController extends Controller
{
    /**
     * Display a listing of the country partner assistant.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CountryPartner $countryPartner)
    {
        return response($countryPartner->load('countryPartnerAssistants')->loadCount('countryPartnerAssistants'), 200);
    }

    /**
     * Store a newly created country partner assistants in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CountryPartner $countryPartner, CreateCountryPartnerAssistantRequest $request)
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
                if(CountryPartnerAssistant::withTrashed()->where('user_id', User::where('username', $data['username'])->value('id'))->exists()){
                    $teacher = CountryPartnerAssistant::withTrashed()->where('user_id', User::where('username', $data['username'])->value('id'))->firstOrFail();
                    $teacher->update(
                        [
                            'country_partner_id'    => $countryPartner->user_id,
                            'country_id'            => $data['country_id'],
                            'deleted_at'            => null
                        ]
                    );
                }else{
                    CountryPartnerAssistant::create(
                        [
                            'user_id'               => User::where('username', $data['username'])->value('id'),
                            'country_partner_id'    => $countryPartner->user_id,
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
        return $this->index($countryPartner);
    }

    /**
     * Display the specified country partner assistant.
     *
     * @param  \App\Models\CountryPartnerAssistant  $CountryPartnerAssistant
     * @return \Illuminate\Http\Response
     */
    public function show(CountryPartnerAssistant $CountryPartnerAssistant, CountryPartner $countryPartner)
    {
        return response($CountryPartnerAssistant, 200);
    }

    /**
     * Update the specified country partner assistant in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CountryPartnerAssistant  $country_partner_assistant
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCountryPartnerAssistantRequest $request, CountryPartnerAssistant $CountryPartnerAssistant)
    {
        $CountryPartnerAssistant->user->update([
            'name'          => $request->name,
            'username'      => $request->username,
            'email'         => $request->email,
            'role_id'       => Role::where('name', $request->role)->value('id'),
            'password'      => bcrypt($request->password),
            'updated_by'    => auth()->id()
        ]);
        $CountryPartnerAssistant->update([
            'country_id'            => $request->country_id,
        ]);
        return response($CountryPartnerAssistant, 200);
    }

    /**
     * Remove the specified country partner assistant from storage.
     *
     * @param  \App\Models\CountryPartnerAssistant  $country_partner_assistant
     * @return \Illuminate\Http\Response
     */
    public function destroy(CountryPartnerAssistant $CountryPartnerAssistant)
    {
        $CountryPartnerAssistant->delete();
        return $this->index($CountryPartnerAssistant->countryPartner);
    }
}
