<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;

class CreateSchoolRequest extends CreateBaseRequest
{
    function __construct()
    {
        $this->key = 'school';
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole(['super admin', 'admin', 'country partner', 'country partner assistant']);
    }

    /**
     * @return arr of rules
     */
    protected function validationRules($key)
    {
        return [
            $key.'.name'                => 'required|string|max:164',
            $key.'.email'               => ['required', 'email', 'max:164', Rule::unique('schools', 'email')->whereNull('deleted_at')],
            $key.'.province'            => 'string|max:64',
            $key.'.address'             => 'string|max:240',
            $key.'.postal_code'         => 'string|max:16',
            $key.'.phone'               => 'required|string|regex:/^[0-9\+]*$/|max:24',                 //Todo limit + to only 1
            $key.'.country_id'          => 'required|digits_between:2,251|exists:countries,id',
            $key.'.is_tuition_centre'   => 'required|boolean'
        ];
    }
}
