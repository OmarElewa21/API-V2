<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Section;
use App\Http\Requests\StoreCollectionRequest;
use App\Http\Requests\UpdateCollectionRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class CollectionController extends Controller
{
    /***************************************** Helpers ****************************************/
    /**
     * Store Tags
     * @param (array) $data
     * @param \App\Models\Collection $collection
     */
    private function storeSections($data, $collection)
    {
        try {
            $section_index = 1;
            foreach($data['tasks'] as $section_data){
                $section = Section::create(array_merge($section_data, [
                    'collection_id'     => $collection->id,
                    'index'             => $section_index
                ]));

                foreach($section_data['tasks'] as $index=>$task_or_group){
                    if(is_array($task_or_group)){
                        foreach($task_or_group as $group_index=>$task_id){
                            DB::table('section_task')->insert([
                                'section_id'       => $section->id,
                                'task_id'          => $task_id,
                                'index'            => $index+1,
                                'in_group'         => 1,
                                'group_index'      => $group_index+1
                            ]);
                        }
                    }else{
                        DB::table('section_task')->insert([
                            'section_id'       => $section->id,
                            'task_id'          => $task_or_group,
                            'index'            => $index+1,
                        ]);
                    }
                }

                $section_index++;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

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
     * @param  \App\Http\Requests\StoreCollectionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCollectionRequest $request)
    {
        DB::beginTransaction();
        foreach($request->all() as $key=>$data){
            try {
                $collection = Collection::create($data);
                if(Arr::has($data, 'tags')){
                    foreach($data['tags'] as $tag){
                        DB::table('collection_tag')->insert([
                            'collection_id'       => $collection->id,
                            'tag_id'              => $tag,
                        ]);
                    }
                }
                $this->storeSections($data, $collection);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }
        DB::commit();
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function show(Collection $collection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCollectionRequest  $request
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCollectionRequest $request, Collection $collection)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function destroy(Collection $collection)
    {
        //
    }
}
