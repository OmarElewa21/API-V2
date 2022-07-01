<?php

namespace App\Http\Controllers;

use App\Models\DifficultyGroup;
use App\Http\Requests\StoreDifficultyGroupRequest;
use App\Http\Requests\UpdateDifficultyGroupRequest;

class DifficultyGroupController extends Controller
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
                'filterOptions.status'           => 'in:Active,Deleted',
            ]);
            $data = DifficultyGroup::applyFilter($request->get('filterOptions'));
        }else{
            $data = new DifficultyGroup;
        }
        $filterOptions = DifficultyGroup::getFilterForFrontEnd($data);
        return response(
            $filterOptions->merge(
                collect(
                    $data->with('levels:difficulty_group_id,name')->withCount('levels')
                    ->paginate(is_numeric($request->paginationNumber) ? $request->paginationNumber : 5)
                )->forget(['links', 'first_page_url', 'last_page_url', 'next_page_url', 'path', 'prev_page_url']))
            ,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDifficultyGroupRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDifficultyGroupRequest $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DifficultyGroup  $difficultyGroup
     * @return \Illuminate\Http\Response
     */
    public function show(DifficultyGroup $difficultyGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDifficultyGroupRequest  $request
     * @param  \App\Models\DifficultyGroup  $difficultyGroup
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDifficultyGroupRequest $request, DifficultyGroup $difficultyGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DifficultyGroup  $difficultyGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(DifficultyGroup $difficultyGroup)
    {
        //
    }
}
