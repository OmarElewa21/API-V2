<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class CountryPartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(User::countryPartners()->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CountryPartner  $countryPartner
     * @return \Illuminate\Http\Response
     */
    public function show(CountryPartner $countryPartner)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CountryPartner  $countryPartner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CountryPartner $countryPartner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CountryPartner  $countryPartner
     * @return \Illuminate\Http\Response
     */
    public function destroy(CountryPartner $countryPartner)
    {
        //
    }
}
