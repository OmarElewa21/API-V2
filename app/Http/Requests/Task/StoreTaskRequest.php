<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\CreateBaseRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends CreateBaseRequest
{
    function __construct()
    {
        $this->key = 'task';
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
            $key.'.title'               => 'required|string|max:132',
            $key.'.identifier'          => 'required|unique:tasks,identifier',
            $key.'.domains'             => 'array',
            $key.'.topics'              => 'array',
            $key.'.tags'                => 'array',
            $key.'.description'         => 'string',
            $key.'.img'                 => 'string',
            $key.'.solution_working'    => 'string',
            $key.'.recommendations'     => 'array',
            $key.'.task_content'        => 'required|array',
            $key.'.task_content.lang_id'    => 'required|exists:languages,id',
            $key.'.task_content.title'      => 'required|string',
            $key.'.task_content.content'    => 'required|string',
            $key.'.answers_as_img'          => 'required|boolean',
            $key.'.answer_type'             => 'required|in:MCQ,Input|bail',
            $key.'.answers'                 => 'required|array|bail',
        ];
        $validation_arr = $this->domains_validation($key, $validation_arr);
        $validation_arr = $this->answers_validation($key, $validation_arr);
        $validation_arr = $this->recommendation_validation($key, $validation_arr);
        return $validation_arr;
    }

    private function domains_validation($key, $validation_arr)
    {
        if(Arr::has($this->get($key), 'domains')){
            foreach($this->get($key)['domains'] as $k=>$domain){
                $validation_arr = array_merge($validation_arr, [
                    $key.'.domains.'.$k      => Rule::exists(\App\Models\DomainsTags::class, 'id')->where('is_tag', 0)->whereNull('deleted_at')
                ]);
            }
        }
        if(Arr::has($this->get($key), 'tags')){
            foreach($this->get($key)['tags'] as $k=>$domain){
                $validation_arr = array_merge($validation_arr, [
                    $key.'.tags.'.$k      => Rule::exists(\App\Models\DomainsTags::class, 'id')->where('is_tag', 1)->whereNull('deleted_at')
                ]);
            }
        }
        if(Arr::has($this->get($key), 'topics')){
            foreach($this->get($key)['topics'] as $k=>$domain){
                $validation_arr = array_merge($validation_arr, [
                    $key.'.topics.'.$k      => Rule::exists(\App\Models\Topic::class, 'id')->whereNull('deleted_at')
                ]);
            }
        }
        return $validation_arr;
    }

    private function answers_validation($key, $validation_arr)
    {
        $answer_type = $this->get($key)['answer_type'];

        foreach($this->get($key)['answers'] as $k=>$answer){
            $validation_arr = array_merge($validation_arr, [
                $key.'.answers.'.$k.'.order'            => 'required|integer|max:'.count($this->get($key)['answers']),
                $key.'.answers.'.$k.'.label'            => 'required|string',
            ]);
        }

        if($answer_type === 'MCQ'){
            $validation_arr = array_merge($validation_arr, [
                $key.'.answer_layout'             => 'required|in:Horizontal,Vertical',
                $key.'.answer_structure'          => 'required|in:Default,Group,Sequence',
                $key.'.answer_sorting'            => 'required|in:Fix Order,Random',
            ]);

            foreach($this->get($key)['answers'] as $k=>$answer){
                $validation_arr = array_merge($validation_arr, [
                    $key.'.answers.'.$k.'.is_correct'       => 'required|boolean',
                ]);
            }
        }else{
            $validation_arr = array_merge($validation_arr, [
                $key.'.answer_structure'          => 'required|in:Default,Group,Open'
            ]);

            foreach($this->get($key)['answers'] as $k=>$answer){
                $validation_arr = array_merge($validation_arr, [
                    $key.'.answers.'.$k.'.content'         => 'string',
                ]);
            }
        }
        return $validation_arr;
    }

    /**
     * @return (array) rules
     */
    private function recommendation_validation($key, $validation_arr)
    {
        $validation_arr = [];
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
