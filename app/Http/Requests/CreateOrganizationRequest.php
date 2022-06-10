<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class CreateOrganizationRequest extends BaseRequest
{
    function __construct()
    {
        $this->key = 'organization';
        $this->unique_fields = ['name', 'email'];
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
        return [
            $key.'.name'                      => ['required', 'string', 'max:164', Rule::unique('organizations', 'name')->whereNull('deleted_at')],
            $key.'.email'                     => ['required', 'email', 'max:164', Rule::unique('organizations', 'email')->whereNull('deleted_at')],
            $key.'.phone'                     => 'required|string|max:24',
            $key.'.person_in_charge_name'     => 'required|string|max:164',
            $key.'.address'                   => 'required|string',
            $key.'.billing_address'           => 'required|string',
            $key.'.shipping_address'          => 'required|string',
            $key.'.img'                       => 'required|string|max:255',
            $key.'.country_id'                => 'required|digits_between:2,251|exists:countries,id'
        ];
    }
}
