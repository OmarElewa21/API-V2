<?php

namespace App\Http\Requests;

class CreateSchoolRequest extends BaseRequest
{
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
        return [
            'name'                  => 'required|string|max:164',
            'address'               => 'required|string|max:240',
            'postal_code'           => 'required|string|max:16',
            'phone'                 => 'required|string|max:24',
            'country_id'            => 'required|digits_between:2,251|exists:countries,id',
            'is_tuition_centre'     => 'required|boolean',
        ];
    }
}
