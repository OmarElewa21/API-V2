<?php

namespace App\Http\Controllers;

use App\Models\DomainsTags;
use App\Http\Requests\StoreDomainsTagsRequest;
use App\Http\Requests\UpdateDomainsTagsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Route;

class DomainsTagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = DomainsTags::withTrashed()->where(function($query){
            $query->where('domains_tags.is_tag', 1)->orwhere(function($query){
                $query->where('domains_tags.is_tag', 0)->whereNotNull('domains_tags.parent_id');
            });
        })->with('domain:id,name,uuid');

        DomainsTags::applyFilter($request, $data);
        $filterOptions = DomainsTags::getFilterForFrontEnd($data);
        
        return response(
            $filterOptions->merge(
                collect($data->select('domains_tags.*')
                    ->paginate(is_numeric($request->paginationNumber) ? $request->paginationNumber : 5)
                )->forget(['links', 'first_page_url', 'last_page_url', 'next_page_url', 'path', 'prev_page_url']))
            ,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDomainsTagsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDomainsTagsRequest $request)
    {
        DB::beginTransaction();
        $is_tag = Route::currentRouteName() === 'domains.store' ? 0 : 1;
        foreach($request->all() as $key=>$data){
            try {
                if(DomainsTags::withTrashed()->where('name', $data['name'])->where('is_tag', $is_tag)->doesntExist())
                {
                    $record = DomainsTags::create(['name' => $data['name'], 'is_tag' => $is_tag]);
                    if(!$is_tag){
                        if(Arr::exists($data, 'topics')){
                            foreach($data['topics'] as $topic){
                                DomainsTags::create([
                                    'name'      => $topic,
                                    'parent_id' => $record->id
                                ]);
                            }
                        }else{
                            DomainsTags::create([
                                'name'      => $record->name,
                                'parent_id' => $record->id
                            ]);
                        }
                    }
                }
                else{
                    $record = DomainsTags::withTrashed()->where('name', $data['name'])->where('is_tag', $is_tag)->first();
                    if($record->status === DomainsTags::STATUS['Deleted']){
                        throw new Exception($record->name . " is deleted, please contact the admin for restoring it");
                    }
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
     * @param  \App\Models\DomainsTags  $domainsTags
     * @return \Illuminate\Http\Response
     */
    public function show(DomainsTags $domainsTags)
    {
        return response(
            $domainsTags->is_tag ? $domainsTags
            : (is_null($domainsTags->parent_id) ? $domainsTags->append('topics')
                : $domainsTags->load('domain:id,name,uuid')),
            200);
    }

    /**
     * Update the specified resource in storage.
     *  
     * @param  \App\Http\Requests\UpdateDomainsTagsRequest  $request
     * @param  \App\Models\DomainsTags  $domainsTags
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDomainsTagsRequest $request, DomainsTags $domainsTags)
    {
        DB::beginTransaction();
        try {
            if($domainsTags->status === DomainsTags::STATUS['Deleted']){
                throw new Exception($domainsTags->name . " is deleted, please contact the admin for restoring it");
            }elseif($domainsTags->status === DomainsTags::STATUS['Pending']){
                throw new Exception($domainsTags->name . " is pending, you can't add to this domain untill it is approved");
            }
            
            $domainsTags->update($request->only('name'));

            if(!$request->is_tag && $request->has('topics')){
                DomainsTags::where('parent_id', $domainsTags->id)->update([
                    'deleted_by' => auth()->id(),
                    'status'     => DomainsTags::STATUS['Deleted']
                ]);
                DomainsTags::where('parent_id', $domainsTags->id)->delete();
                foreach($request->topics as $topic){
                    if(DomainsTags::withTrashed()->where('name', $topic)->where('parent_id', $domainsTags->id)->exists()){
                        $record = DomainsTags::withTrashed()->where('name', $topic)->where('parent_id', $domainsTags->id)->first();
                        $record->update([
                            'name'          => $topic,
                            'deleted_at'    => null,
                            'deleted_by'    => null    
                        ]);
                    }else{
                        DomainsTags::create([
                            'name'          => $topic,
                            'parent_id'     => $domainsTags->id
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response($e->getMessage(), 500);
        }
        DB::commit();
        return $this->show($domainsTags);
    }

    public function update_topic(DomainsTags $topic, Request $request)
    {
        $request->validate = array('name' => 'required|string' );
        $topic->name = $request->name;
        $topic->save();
        return $this->show($topic);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DomainsTags  $domainsTags
     * @param boolean return (Whether to return data or just a success of the destroy)
     * @return \Illuminate\Http\Response
     */
    public function destroy(DomainsTags $domainsTags)
    {
        try {
            DB::transaction(function ()use($domainsTags) {
                DomainsTags::where('parent_id', $domainsTags->id)->update([
                    'deleted_by' => auth()->id(),
                    'status'     => DomainsTags::STATUS['Deleted']
                ]);
                DomainsTags::where('parent_id', $domainsTags->id)->delete();
                $domainsTags->delete();
            });

        } catch (Exception $e) {
            return response($e->getMessage(), 500);
        }
        
        return $this->index(new Request);
    }

    /**
     * approve multiple schools.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massApprove(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach($request->all() as $domain_uuid){
                if(Str::isUuid($domain_uuid) && DomainsTags::whereUuid($domain_uuid)->exists()){
                    $domain = DomainsTags::whereUuid($domain_uuid)->firstOrFail();
                    $domain->update([
                        'status'        => DomainsTags::STATUS['Approved'],
                        'approved_by'   => auth()->id(),
                        'approved_at'   => now()
                    ]);
                }else{
                    throw new Exception("data is not valid");
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
        DB::commit();
        return $this->index(new Request);
    }

    /**
     * Remove multiple schools.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach($request->all() as $domain_uuid){
                if(Str::isUuid($domain_uuid) && DomainsTags::domains()->whereUuid($domain_uuid)->exists()){
                    $domainsTags = DomainsTags::domains()->whereUuid($domain_uuid)->first();
                    DomainsTags::where('parent_id', $domainsTags->id)->update([
                        'deleted_by' => auth()->id(),
                        'status'     => DomainsTags::STATUS['Deleted']
                    ]);
                    DomainsTags::where('parent_id', $domainsTags->id)->delete();
                    $domainsTags->delete();
                }else{
                    throw new Exception("data is not valid");
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
        DB::commit();
        return $this->index(new Request);
    }
}
