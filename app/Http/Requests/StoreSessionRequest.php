<?php

namespace App\Http\Requests;

class StoreSessionRequest extends CreateBaseRequest
{
    function __construct()
    {
        $this->key = 'session';
        $this->unique_fields = ['name'];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return arr of rules
     */
    protected function validationRules($key)
    {
        return [
            $key.'.name'            => 'required|string|max:132',
            $key.'.invigilator'     => 'array',
        ];
    }
}
