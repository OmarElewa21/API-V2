<?php

namespace App\Http\Requests;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class StoreCollectionRequest extends CreateBaseRequest
{
    function __construct()
    {
        $this->key = 'collection';
        $this->unique_fields = ['identifier'];
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
     * @return arr of rules
     */
    protected function validationRules($key)
    {
        $validation_arr = [
            $key.'.name'                => 'required|string|max:132',
            $key.'.identifier'          => 'required|unique:collections,identifier',
            $key.'.tags'                => 'array',
            $key.'.description'         => 'string',
            $key.'.time_to_solve'       => 'integer',
            $key.'.initial_points'      => 'integer',
            $key.'.recommendations'     => 'array',
            $key.'.tasks'               => 'required|array|bail',
        ];

        $validation_arr = $this->tags_validation($key, $validation_arr);
        $validation_arr = $this->recommendation_validation($key, $validation_arr);
        $validation_arr = $this->tasks_validation($key, $validation_arr);
        return $validation_arr;
    }

    /**
     * @return (array) rules
     */
    private function tags_validation($key, $validation_arr)
    {
        if(Arr::has($this->get($key), 'tags')){
            foreach($this->get($key)['tags'] as $k=>$tag){
                $validation_arr = array_merge($validation_arr, [
                    $key.'.tags.'.$k      => Rule::exists(\App\Models\DomainsTags::class, 'id')->where('is_tag', 1)->whereNull('deleted_at')
                ]);
            }
        }
        return $validation_arr;
    }

     /**
     * @return (array) rules
     */
    private function tasks_validation($key, $validation_arr)
    {
        if(Arr::has($this->get($key), 'tasks')){
            foreach($this->get($key)['tasks'] as $sec_key=>$section){
                $validation_arr = array_merge($validation_arr, [
                    $key.'.tasks.'.$sec_key                         => 'array',
                    $key.'.tasks.'. $sec_key . '.tasks'             => 'required|array',
                    $key.'.tasks.'. $sec_key . '.sort_randomly'     => 'required|boolean',
                    $key.'.tasks.'. $sec_key . '.allow_skips'       => 'required|boolean',
                    $key.'.tasks.'. $sec_key . '.description'       => 'string'
                ]);
            }
            
            foreach($section['tasks'] as $task_key=>$task){
                if(is_array($task)){
                    foreach($task as $group_key=>$group){
                        $validation_arr = array_merge($validation_arr, [
                            $key.'.tasks.'.$sec_key . '.tasks.' . $task_key . '.' . $group_key      => 'integer|exists:tasks,id'
                        ]);
                    }
                }else{
                    $validation_arr = array_merge($validation_arr, [
                        $key.'.tasks.'.$sec_key . '.tasks.' . $task_key      => 'integer|exists:tasks,id'
                    ]);
                }   
            }
            
        }
        return $validation_arr;
    }
    
    /**
     * @return (array) rules
     */
    private function recommendation_validation($key, $validation_arr)
    {
        if(Arr::has($this->get($key), 'recommendations')){
            foreach($this->get($key)['recommendations'] as $k=>$data){
                $validation_arr = array_merge($validation_arr, [
                    $key.'.recommendations.'.$k                  => 'array',
                    $key.'.recommendations.'.$k.'.grades'        => 'required|array',
                    $key.'.recommendations.'.$k.'.difficulty'    => 'required|array',
                ]);
                foreach($data['grades'] as $k2=>$grade){
                    $validation_arr = array_merge($validation_arr, [
                        $key.'.recommendations.'.$k.'.grades.'.$k2     => 'in:Grade 1,Grade 2,Grade 3,Grade 4,Grade 5,Grade 6',
                        $key.'.recommendations.'.$k.'.difficulty.'.$k2 => 'in:Easy,Intermediate,Hard,Very Hard',
                    ]);
                }
            }
        }
        return $validation_arr;
    }
}
