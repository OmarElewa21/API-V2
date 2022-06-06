<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Http\Requests\CreateOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use Illuminate\Support\Str;
use Exception;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(Organization::get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateOrganizationRequest $request)
    {
        if(Organization::withTrashed()->where('email', $request->email)->exists()){
            $organization = Organization::withTrashed()->where('email', $request->email)->first();
            $organization->deleted_at = null;
        }else{
            $organization = New Organization;
        }
        try {
            $organization->fill($request->all())->save();
            return response()->json($organization, 200);
        } catch (Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {
        return response($organization, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        try {
            $organization->fill($request->all())->save();
            return response()->json($organization, 200);
        } catch (Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization)
    {
        try {
            $organization->delete();
            return $this->index();
        } catch (Exception $e) {
            response($e->getMessage(), 500);
        }
    }
}
