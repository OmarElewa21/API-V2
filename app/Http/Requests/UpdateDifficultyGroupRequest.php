<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateDifficultyGroupRequest extends FormRequest
{
    /**
     * @var group
     */
    private $group;

    /**
     *
     * @param Route $route
     */
    function __construct(Route $route)
    {
        $this->group = $route->parameter('difficulty_group');
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $arr = [
            'name'                => ['required','string','max:132', Rule::unique('difficulty_groups')->ignore($this->group)],
            'has_default_marks'   => 'required|boolean',
            'levels'              => 'required|array'
        ];

        foreach($this->levels as $k=>$level){
            $arr = array_merge($arr, [
                'levels.'.$k.'.name'    => 'required|string'
            ]);

            if($this->has_default_marks){
                $arr = array_merge($arr, [
                    'levels.'.$k.'.correct_points'  => 'required|integer',
                    'levels.'.$k.'.wrong_points'    => 'required|integer',
                    'levels.'.$k.'.blank_points'    => 'required|integer'
                ]);
            }
        }
        return $arr;
    }
}
