<?php

namespace App\Http\Controllers;

use App\Models\DomainsTags;
use App\Models\Topic;
use App\Http\Requests\StoreDomainsTagsRequest;
use App\Http\Requests\UpdateDomainsTagsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DomainsTagsController extends Controller
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
                'filterOptions'                => 'array',
                'filterOptions.type'           => 'in:Domain,Tag',
                'filterOptions.status'         => 'in:approved,pending,rejected,deleted',
            ]);
            $data = DomainsTags::applyFilter($request->get('filterOptions'));
        }else{
            $data = new DomainsTags;
        }
        $filterOptions = DomainsTags::getFilterForFrontEnd($data);
        return response(
            $filterOptions->merge(
                collect(
                    $data
                    ->leftJoinRelationship('topics')
                    ->select('domains_tags.*', 'topics.name as topic_name')
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
        foreach($request->all() as $key=>$data){
            try {
                if
                (
                    $data['is_tag'] && DomainsTags::tags()->where('name', $data['name'])->doesntExist()
                    || !$data['is_tag'] && DomainsTags::domains()->where('name', $data['name'])->doesntExist()
                )
                {
                    DomainsTags::create(Arr::only($data, ['name', 'is_tag']));
                }
                
                if(!$data['is_tag'] && Arr::exists($data, 'topics')){
                    foreach($data['topics'] as $topic){
                        if
                        (Topic::where('name', $topic)
                            ->whereRelation('domain', 'name', '=', $data['name'])
                            ->doesntExist()
                        )
                        {
                            Topic::create([
                                'name'      => $topic,
                                'domain_id' => DomainsTags::domains()->where('name', $data['name'])->value('id')
                            ]);
                        }
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
        $data = $domainsTags->is_tag ? $domainsTags : $domainsTags->load('topics:domain_id,uuid,name');
        return response($data, 200);
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
        $domainsTags->update(array_merge($request->only('name'), ['updated_by'  => auth()->id()]));
        if(!$request->is_tag && $request->has('topics')){
            $domainsTags->topics()->update(['deleted_by' => auth()->id()]);
            $domainsTags->topics()->delete();
            foreach($request->topics as $topic){
                if
                (Topic::where('name', $topic)
                    ->where('domain_id', $domainsTags->id)
                    ->doesntExist()
                )
                {
                    Topic::create([
                        'name'      => $topic,
                        'domain_id' => $domainsTags->id
                    ]);
                }
            }
        }
        return $this->show($domainsTags);
    }

    public function update_topic($topic_uuid, Request $request)
    {
        $request->validate = array(
            'name' =>   'required|string' 
        );
        $topic = Topic::whereUuid($topic_uuid)->firstOrFail();
        $topic->update(['name' => $request->name, 'updated_by' => auth()->id()]);
        return $topic;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DomainsTags  $domainsTags
     * @param boolean return (Whether to return data or just a success of the destroy)
     * @return \Illuminate\Http\Response
     */
    public function destroy(DomainsTags $domainsTags, $return=true)
    {
        $domainsTags->topics()->update(['deleted_by' => auth()->id()]);
        $domainsTags->topics()->delete();
        $domainsTags->update(['deleted_by' => auth()->id()]);
        $domainsTags->delete();
        return $return ? $this->index(new Request) : true;
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
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now()
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
                if(Str::isUuid($domain_uuid) && DomainsTags::whereUuid($domain_uuid)->exists()){
                    $this->destroy(DomainsTags::whereUuid($domain_uuid)->first());
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
