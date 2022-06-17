<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Route;

class UpdateSchoolRequest extends CreateSchoolRequest
{
    /**
     * @var school
     */
    private $school;

    /**
     *
     * @param Route $route
     */
    function __construct(Route $route)
    {
        $this->school = $route->parameter('school');
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'                => 'required|string|max:164',
            'email'               => ['required', 'email', 'max:164', Rule::unique('schools', 'email')->ignore($this->school)],
            'province'            => 'string|max:64',
            'address'             => 'required|string|max:240',
            'postal_code'         => 'required|string|max:16',
            'phone'               => 'required|string|max:24',
            'country_id'          => 'required|digits_between:2,251|exists:countries,id',
            'is_tuition_centre'   => 'required|boolean'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
    }
}
