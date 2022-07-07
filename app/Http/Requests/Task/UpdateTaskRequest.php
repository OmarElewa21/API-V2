<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Route;

class UpdateTaskRequest extends FormRequest
{
    private $task;
    private $routeName;

    /**
     *
     * @param Route $route
     */
    function __construct(Route $route)
    {
        $this->task = $route->parameter('task');
        $this->routeName = $route->getName();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole(['super admin', 'admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        switch ($this->routeName) {
            case 'tasks.updateTask':
                return $this->updateTask();
                break;
            case 'tasks.updateTaskContent':
                return $this->updateTaskContent();
                break;
            case 'tasks.updateRecommendations':
                return $this->updateRecommendations();
                break;
            case 'tasks.updateAnswers':
                return $this->updateAnswers();
                break;
            default:
                return [];
                break;
        }
    }

    /**
     * @return (array) rules
     */
    private function updateTask()
    {
        $validation_arr = [
            'title'               => 'required|string|max:132',
            'identifier'          => ['required', Rule::unique('tasks')->ignore($this->task)],
            'domains'             => 'array',
            'topics'              => 'array',
            'tags'                => 'array',
            'description'         => 'string',
            'img'                 => 'string',
            'solution_working'    => 'string',
        ];

        if($this->has('domains')){
            foreach($this->get('domains') as $k=>$domain){
                $validation_arr = array_merge($validation_arr, [
                    'domains.'.$k      => Rule::exists(\App\Models\DomainsTags::class, 'id')->where('is_tag', 0)->whereNull('deleted_at')
                ]);
            }
        }
        if($this->has('tags')){
            foreach($this->get('tags') as $k=>$tag){
                $validation_arr = array_merge($validation_arr, [
                    'tags.'.$k      => Rule::exists(\App\Models\DomainsTags::class, 'id')->where('is_tag', 1)->whereNull('deleted_at')
                ]);
            }
        }
        if($this->get('topics')){
            foreach($this->get('topics') as $k=>$topic){
                $validation_arr = array_merge($validation_arr, [
                    'topics.'.$k      => Rule::exists(\App\Models\Topic::class, 'id')->whereNull('deleted_at')
                ]);
            }
        }
        return $validation_arr;
    }

    /**
     * @return (array) rules
     */
    private function updateTaskContent()
    {
        $validation_arr = [];
        foreach($this->all() as $key=>$data){
            $validation_arr = array_merge($validation_arr, [
                $key               => 'array',
                $key.'.lang_id'    => 'required|exists:languages,id',
                $key.'.title'      => 'required|string',
                $key.'.content'    => 'required|string',
            ]);
        }
        return $validation_arr;
    }

    /**
     * @return (array) rules
     */
    private function updateRecommendations()
    {
        $validation_arr = [];
        foreach($this->all() as $key=>$data){
            $validation_arr = array_merge($validation_arr, [
                $key               => 'array',
                $key.'.grades'     => 'required|array',
                $key.'.difficulty' => 'required|array',
            ]);
            foreach($data['grades'] as $k=>$grade){
                $validation_arr = array_merge($validation_arr, [
                    $key.'.grades.'.$k     => 'in:Grade 1,Grade 2,Grade 3,Grade 4,Grade 5,Grade 6',
                    $key.'.difficulty.'.$k => 'in:Easy,Intermediate,Hard,Very Hard',
                ]);
            }
        }
        return $validation_arr;
    }
    
    /**
     * @return (array) rules
     */
    private function updateAnswers()
    {
        $validation_arr = [
            'answers_as_img'          => 'required|boolean',
            'answer_type'             => 'required|in:MCQ,Input|bail',
            'answers'                 => 'required|array|bail',
        ];

        $answer_type = $this->get('answer_type');

        foreach($this->get('answers') as $k=>$answer){
            $validation_arr = array_merge($validation_arr, [
                'answers.'.$k.'.order'            => 'required|integer|max:'.count($this->get('answers')),
                'answers.'.$k.'.label'            => 'required|string',
            ]);
        }

        if($answer_type === 'MCQ'){
            $validation_arr = array_merge($validation_arr, [
                'answer_layout'             => 'required|in:Horizontal,Vertical',
                'answer_structure'          => 'required|in:Default,Group,Sequence',
                'answer_sorting'            => 'required|in:Fix Order,Random',
            ]);

            foreach($this->get('answers') as $k=>$answer){
                $validation_arr = array_merge($validation_arr, [
                    'answers.'.$k.'.is_correct'       => 'required|boolean',
                ]);
            }
        }else{
            $validation_arr = array_merge($validation_arr, [
                'answer_structure'          => 'required|in:Default,Group,Open'
            ]);

            foreach($this->get('answers') as $k=>$answer){
                $validation_arr = array_merge($validation_arr, [
                    'answers.'.$k.'.content'         => 'string',
                ]);
            }
        }
        return $validation_arr;
    }
}
