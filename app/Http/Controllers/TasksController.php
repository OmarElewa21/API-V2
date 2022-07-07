<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAnswer;
use App\Models\TaskAnswerContent;
use App\Models\TaskContent;
use App\Http\Requests\Task\ValidateFilterOptionsRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class TasksController extends Controller
{
    /***************************************** Helpers ****************************************/
    /**
     * Store Domains, Tags and Topics
     * @param (array) $data
     * @param \App\Models\Tasks $task
     */
    private function storeDomainsAndTopics($data, $task)
    {
        try {
            if(Arr::has($data, 'domains')){
                foreach($data['domains'] as $domain){
                    DB::table('task_domains')->insert([
                        'task_id'       => $task->id,
                        'relation_id'   => $domain,
                        'relation_type' => 'App\Models\DomainsTags'
                    ]);
                }
            }
            if(Arr::has($data, 'topics')){
                foreach($data['topics'] as $topic){
                    DB::table('task_domains')->insert([
                        'task_id'       => $task->id,
                        'relation_id'   => $topic,
                        'relation_type' => 'App\Models\Topic'
                    ]);
                }
                if(Arr::has($data, 'tags')){
                    foreach($data['tags'] as $topic){
                        DB::table('task_domains')->insert([
                            'task_id'       => $task->id,
                            'relation_id'   => $topic,
                            'relation_type' => 'App\Models\DomainsTags',
                            'is_tag'        => 1
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Store 
     * @param (array) $data
     * @param \App\Models\Tasks $task
     */
    private function storeTaskAnswers($data, $task, $withUpdatedBy=false)
    {
        try {
            foreach($data['answers'] as $answer){
                $task_answer = TaskAnswer::create(array_merge($answer, [
                    'task_id'    => $task->id,
                    'is_img'     => (boolean)$data['answers_as_img'] ? 1 : 0,
                    'updated_by' => $withUpdatedBy ? auth()->id() : null
                ]));
                TaskAnswerContent::create(array_merge($answer, [
                    'answer_id'  => $task_answer->id,
                    'updated_by' => $withUpdatedBy ? auth()->id() : null,
                    'updated_at' => $withUpdatedBy ? now() : null
                ]));
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
    public function index(ValidateFilterOptionsRequest $request)
    {
        $data = Task::with(['domains:id,name', 'domains.topics:domain_id,name','tags:id,name'])
                ->withCount(['task_content', 'task_answers as correct_answers_count' => function($q){
                    $q->where('is_correct', 1);
                }]);

        if($request->has('filterOptions')){
            $data = Task::applyFilter($request->get('filterOptions'), $data);
        }
        
        $filterOptions = Task::getFilterForFrontEnd($data);        // get collection of availble filter options data
        
        // Get data as a collection
        $data = collect($data->paginate(is_numeric($request->paginationNumber) ? $request->paginationNumber : 5))
                    ->forget(['links', 'first_page_url', 'last_page_url', 'next_page_url', 'path', 'prev_page_url']);
        return response($filterOptions->merge($data), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Task\StoreTaskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        DB::beginTransaction();
        foreach($request->all() as $key=>$data){
            try {
                $task = Task::create($data);
                $this->storeDomainsAndTopics($data, $task);
                TaskContent::create(array_merge($data['task_content'], ['task_id' => $task->id]));
                $this->storeTaskAnswers($data, $task);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }
        DB::commit();
        return $this->index(new ValidateFilterOptionsRequest);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        return $task->load(['domains:id,name', 'domains.topics:domain_id,name','tags:id,name'])
                    ->loadCount(['task_content', 'task_answers as correct_answers_count' => function($q){
                        $q->where('is_correct', 1);
                }]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function updateTask(UpdateTaskRequest $request, Task $task)
    {
        DB::beginTransaction();
        try{
            $task->update(array_merge($request->all(), ['updated_by' => auth()->id()]));
            DB::table('task_domains')->where('task_id', $task->id)->delete();
            $this->storeDomainsAndTopics($request->all(), $task);
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
        DB::commit();
        return $this->show($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function updateTaskContent(UpdateTaskRequest $request, Task $task)
    {
        DB::beginTransaction();
        try{
            $data = collect($request->all());
            $data = $data->map(function ($array, $key) use($task) {
                return $array = array_merge($array, [
                    'task_id'    => $task->id,
                    'updated_by' => auth()->id(),
                    'updated_at' => now()
                ]);
            });
            foreach($data->all() as $record){
                DB::table('task_contents')->updateOrInsert(
                    ['task_id' => $record['task_id'], 'lang_id' => $record['lang_id']],
                    $record
                );
            }
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
        DB::commit();
        return $this->show($task);   
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function updateRecommendations(UpdateTaskRequest $request, Task $task)
    {
        $task->update([
            'recommendations'  => $request->all(),
            'updated_by'       => auth()->id()
        ]);
        return $this->show($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function updateAnswers(UpdateTaskRequest $request, Task $task)
    {
        DB::beginTransaction();
        try {
            $task->update(array_merge($request->all(), ['updated_at' => auth()->id()]));
            $task->task_answers()->delete();
            $this->storeTaskAnswers($request->all(), $task, true);
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
        DB::commit();
        return $this->show($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task, $return=true)
    {
        $task->update(['deleted_by' => auth()->id()]);
        $task->delete();
        return $return ? $this->index(new ValidateFilterOptionsRequest) : '';
    }

    public function massDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach($request->all() as $task_uuid){
                if(Str::isUuid($task_uuid) && Task::whereUuid($task_uuid)->exists()){
                    $task = Task::whereUuid($task_uuid)->firstOrFail();
                    $this->destroy($task, false);
                }else{
                    throw new Exception("data is not valid");
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
        DB::commit();
        return $this->index(new ValidateFilterOptionsRequest);
    }
}
