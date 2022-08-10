<?php

namespace App\Http\Requests\Competition;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Collection;
use App\Models\Task;

class StoreDifficultyAndPointsRequest extends FormRequest
{
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
        $rules = [
            'collection_id'     => 'required|exists:collections,id',
            'tasks'             => 'required|array'
        ];

        foreach($this->tasks as $k=>$task){
            $rules = array_merge($rules, [
                "tasks.$k.task_id"                      => 'required|exists:tasks,id',
                "tasks.$k.difficulty_group_level_id"    => 'required|exists:difficulty_group_levels,id',
                "tasks.$k.correct_points"               => 'array',
                "tasks.$k.wrong_points"                 => 'numeric',
                "tasks.$k.blank_points"                 => 'numeric',
                "tasks.$k.min_points"                   => 'numeric',
                "tasks.$k.max_points"                   => 'numeric',
            ]);
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $collection = Collection::find($this->collection_id);
        $tasks_ids = $collection->sections()->with('tasks')->get()
            ->pluck('tasks')->flatten()->map(function ($item, $key) {
                return $item['id'];
            })->unique();

        $validator->after(function ($validator) use($tasks_ids) {
            foreach($this->tasks as $key=>$data){
                if(!$tasks_ids->contains(Task::find($data['task_id'])->id)){
                    $validator->errors()->add('errors', 'one task does not belong to the collection given');
                };
            }
        });
    }

    /**
     * Overwrite validation return
     *
     * @return HttpResponseException
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
