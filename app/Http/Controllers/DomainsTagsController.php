<?php

namespace App\Http\Controllers;

use App\Models\DomainsTags;
use App\Http\Requests\StoreDomainsTagsRequest;
use App\Http\Requests\UpdateDomainsTagsRequest;

class DomainsTagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDomainsTagsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDomainsTagsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DomainsTags  $domainsTags
     * @return \Illuminate\Http\Response
     */
    public function show(DomainsTags $domainsTags)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DomainsTags  $domainsTags
     * @return \Illuminate\Http\Response
     */
    public function destroy(DomainsTags $domainsTags)
    {
        //
    }
}
