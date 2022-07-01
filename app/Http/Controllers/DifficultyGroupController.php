<?php

namespace App\Http\Controllers;

use App\Models\DifficultyGroup;
use App\Models\DifficultyGroupLevel;
use App\Http\Requests\StoreDifficultyGroupRequest;
use App\Http\Requests\UpdateDifficultyGroupRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Str;

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
            $data = DifficultyGroup::withTrashed();
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
        DB::beginTransaction();
        foreach($request->all() as $key=>$data){
            try {
                if(DifficultyGroup::withTrashed()->where('name', $data['name'])->exists()){
                    DifficultyGroup::withTrashed()->where('name', $data['name'])->update([
                        'name'              => $data['name'],
                        'has_default_marks' => $data['has_default_marks'],
                        'deleted_by'        => null,
                        'deleted_at'        => null,
                        'status'            => 'active',
                    ]);
                }else{
                    DifficultyGroup::create([
                        'name'              => $data['name'],
                        'has_default_marks' => $data['has_default_marks']
                    ]);
                }

                foreach($data['levels'] as $level){
                    DifficultyGroupLevel::create(array_merge($level, [
                        'difficulty_group_id'   => DifficultyGroup::where('name', $data['name'])->value('id')
                    ]));
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
     * @param  \App\Models\DifficultyGroup  $difficultyGroup
     * @return \Illuminate\Http\Response
     */
    public function show(DifficultyGroup $difficultyGroup)
    {
        return response($difficultyGroup->load('levels')->loadCount('levels'), 200);
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
        DB::beginTransaction();
        try {
            $difficultyGroup->update([
                'name'              => $request->name,
                'has_default_marks' => $request->has_default_marks,
                'updated_by'        => auth()->id(),
                'updated_at'        => now(),
            ]);
            $difficultyGroup->levels()->delete();
            
            foreach($request->levels as $level){
                DifficultyGroupLevel::create(array_merge($level, [
                    'difficulty_group_id'   => $difficultyGroup->id
                ]));
            }

        } catch (Exception $e) {
            DB::rollBack();
            return response($e->getMessage(), 500);
        }
        DB::commit();
        return $this->show($difficultyGroup);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DifficultyGroup  $difficultyGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(DifficultyGroup $difficultyGroup)
    {
        DB::beginTransaction();
        try {
            $difficultyGroup->update([
                'status'            => 'Deleted',
                'deleted_by'        => auth()->id(),
            ]);
            $difficultyGroup->levels()->delete();
            $difficultyGroup->delete();

        } catch (Exception $e) {
            DB::rollBack();
            return response($e->getMessage(), 500);
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
            foreach($request->all() as $group_uuid){
                if(Str::isUuid($group_uuid) && DifficultyGroup::whereUuid($group_uuid)->exists()){
                    $this->destroy(DifficultyGroup::whereUuid($group_uuid)->first());
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
