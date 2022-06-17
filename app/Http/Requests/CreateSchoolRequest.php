<?php

namespace App\Http\Requests;

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
            $key.'.address'             => 'required|string|max:240',
            $key.'.postal_code'         => 'required|string|max:16',
            $key.'.phone'               => 'required|string|max:24',
            $key.'.country_id'          => 'required|digits_between:2,251|exists:countries,id',
            $key.'.is_tuition_centre'   => 'required|boolean'
        ];
    }
}
