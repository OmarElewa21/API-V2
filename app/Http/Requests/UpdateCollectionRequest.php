<?php

namespace App\Http\Requests;

use Illuminate\Routing\Route;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCollectionRequest extends FormRequest
{
    /**
     * @var collection
     */
    private $collection;

    /**
     *
     * @param Route $route
     */
    function __construct(Route $route)
    {
        $this->collection = $route->parameter('collection');
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
        $validation_arr = [
            'name'                => 'required|string|max:132',
            'identifier'          => ['required', Rule::unique('collections')->ignore($this->collection)],
            'tags'                => 'array',
            'description'         => 'string',
            'time_to_solve'       => 'integer',
            'initial_points'      => 'integer',
            'recommendations'     => 'array',
            'tasks'               => 'required|array|bail',
        ];

        $validation_arr = $this->tags_validation($validation_arr);
        $validation_arr = $this->recommendation_validation($validation_arr);
        $validation_arr = $this->tasks_validation($validation_arr);
        return $validation_arr;
    }

    /**
     * @return (array) rules
     */
    private function tags_validation($validation_arr)
    {
        if($this->has('tags')){
            foreach($this->get('tags') as $k=>$tag){
                $validation_arr = array_merge($validation_arr, [
                    'tags.'.$k      => Rule::exists(\App\Models\DomainsTags::class, 'id')->whereNull('deleted_at')
                ]);
            }
        }
        return $validation_arr;
    }

     /**
     * @return (array) rules
     */
    private function recommendation_validation($validation_arr)
    {
        if($this->has('recommendations')){
            foreach($this->get('recommendations') as $k=>$data){
                $validation_arr = array_merge($validation_arr, [
                    'recommendations.'.$k                  => 'array',
                    'recommendations.'.$k.'.grades'        => 'required|array',
                    'recommendations.'.$k.'.difficulty'    => 'required|array',
                ]);
                foreach($data['grades'] as $k2=>$grade){
                    $validation_arr = array_merge($validation_arr, [
                        'recommendations.'.$k.'.grades.'.$k2     => 'in:Grade 1,Grade 2,Grade 3,Grade 4,Grade 5,Grade 6',
                        'recommendations.'.$k.'.difficulty.'.$k2 => 'in:Easy,Intermediate,Hard,Very Hard',
                    ]);
                }
            }
        }
        return $validation_arr;
    }

    /**
     * @return (array) rules
     */
    private function tasks_validation($validation_arr)
    {
        if($this->has('tasks')){
            foreach($this->get('tasks') as $sec_key=>$section){
                $validation_arr = array_merge($validation_arr, [
                    'tasks.'. $sec_key                        => 'array',
                    'tasks.'. $sec_key . '.tasks'             => 'required|array',
                    'tasks.'. $sec_key . '.sort_randomly'     => 'required|boolean',
                    'tasks.'. $sec_key . '.allow_skips'       => 'required|boolean',
                    'tasks.'. $sec_key . '.description'       => 'string'
                ]);
            }
            
            foreach($section['tasks'] as $task_key=>$task){
                if(is_array($task)){
                    foreach($task as $group_key=>$group){
                        $validation_arr = array_merge($validation_arr, [
                            'tasks.'.$sec_key . '.tasks.' . $task_key . '.' . $group_key      => 'integer|exists:tasks,id'
                        ]);
                    }
                }else{
                    $validation_arr = array_merge($validation_arr, [
                        'tasks.'.$sec_key . '.tasks.' . $task_key      => 'integer|exists:tasks,id'
                    ]);
                }   
            }
            
        }
        return $validation_arr;
    }
}
