<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDifficultyGroupRequest extends CreateBaseRequest
{
    function __construct()
    {
        $this->key = 'group';
        $this->unique_fields = ['name'];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole('super admin') || auth()->user()->hasRole('admin');
    }

    /**
     * @return arr of rules
     */
    protected function validationRules($key)
    {
        $arr = [
            $key.'.name'                => ['required','string','max:132', Rule::unique('difficulty_groups', 'name')->whereNull('deleted_at')],
            $key.'.has_default_marks'   => 'required|boolean',
            $key.'.levels'              => 'required|array'
        ];

        foreach($this->get($key)['levels'] as $k=>$level){
            $arr = array_merge($arr, [
                $key.'.levels.'.$k.'.name'    => 'required|string'
            ]);

            if($this->get($key)['has_default_marks']){
                $arr = array_merge($arr, [
                    $key.'.levels.'.$k.'.correct_points'    => 'required|integer',
                    $key.'.levels.'.$k.'.wrong_points'    => 'required|integer',
                    $key.'.levels.'.$k.'.blank_points'    => 'required|integer'
                ]);
            }
        }
        return $arr;
    }
}
