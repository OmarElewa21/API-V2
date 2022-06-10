<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolRequest extends CreateSchoolRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'                => 'required|string|max:164',
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
