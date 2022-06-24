<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Http\Requests\CreateOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Exception;
use App\Http\Scopes\UserScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->has('filterOptions')){
            $request->validate([
                'filterOptions'                 => 'array',
                'filterOptions.country'         => 'exists:countries,id',
            ]);
            $organizations = Organization::applyFilter($request->get('filterOptions'));
        }else{
            $organizations = new Organization;
        }
        return response(collect(
                $organizations
                ->join('countries', 'organizations.country_id', 'countries.id')
                ->select('organizations.*', 'countries.name as country')
                ->withCount('country_partners')
                ->paginate(is_numeric($request->paginationNumber) ? $request->paginationNumber : 5)
            )->forget(['links', 'first_page_url', 'last_page_url', 'next_page_url', 'path', 'prev_page_url'])
            ,200);    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateOrganizationRequest $request)
    {
        DB::beginTransaction();
        foreach($request->all() as $key=>$data){
            try {
                if(Organization::withTrashed()->where('name', $data['name'])->orWhere('email', $data['email'])->exists()){
                    $organization = Organization::withTrashed()->where('name', $data['name'])->orWhere('email', $data['email'])->firstOrFail();
                    $organization->update(
                        array_merge(
                        $data,
                        ['deleted_at' => null, 'updated_by' => auth()->id(), 'deleted_by' => null])
                    );
                }else{
                    Organization::create(
                        array_merge($data, ['created_by' => auth()->id()])
                    );
                }
            } catch (Exception $e) {
                DB::rollBack();
                return response($e->getMessage(), 500);
            }
        }
        DB::commit();
        return $this->index(new Request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {
        return response()->json(
            $organization->load([
                    'country:id,name', 'country_partners' => function($query){
                        $query->withoutGlobalScopes([UserScope::class])->select('user_id', 'organization_id');
                    },
                    'country_partners.user:id,uuid,name'
                ])->loadCount('country_partners')->makeVisible('img'), 
            200);
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
            $organization->fill(array_merge($request->all(), ['updated_by' => auth()->id()]))->save();
            return $this->show($organization);

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
            $organization->update(['deleted_by' => auth()->id()]);
            $organization->delete();
            return $this->index(new Request);
        } catch (Exception $e) {
            response($e->getMessage(), 500);
        }
    }

    /**
     * Remove multiple organizations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach($request->all() as $organization_uuid){
                if(Str::isUuid($organization_uuid) && Organization::whereUuid($organization_uuid)->exists()){
                    $organization = Organization::whereUuid($organization_uuid)->firstOrFail();
                    $organization->update(['deleted_by' => auth()->id()]);
                    $organization->delete();
                }else{
                    throw new Exception("data is not valid");
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response($e->getMessage(), 500);
        }
        DB::commit();
        return $this->index(new Request);
    }
}
