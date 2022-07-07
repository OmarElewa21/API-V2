<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAnswer;
use App\Models\TaskAnswerContent;
use App\Models\TaskContent;
use App\Http\Requests\Task\ValidateFilterOptionsRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

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
    private function storeTaskAnswers($data, $task)
    {
        try {
            foreach($data['answers'] as $answer){
                $task_answer = TaskAnswer::create(array_merge($answer, [
                    'task_id'   => $task->id,
                    'is_img'    => (boolean)$data['answers_as_img'] ? 1 : 0
                ]));
                TaskAnswerContent::create(array_merge($answer, [
                    'answer_id'  => $task_answer->id,
                    'lang_id'    => $data['task_content']['lang_id'],
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
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        //
    }
}
