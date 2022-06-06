<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrganizationRequest extends BaseRequest
{
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'                      => 'required|string|max:64',
            'email'                     => ['required', 'email', 'max:64', Rule::unique('organizations')->whereNull('deleted_at')],
            'phone'                     => 'required|string|max:64',
            'person_in_charge_name'     => 'required|string|max:64',
            'address'                   => 'required|string',
            'billing_address'           => 'required|string',
            'shipping_address'          => 'required|string',
            'img'                       => 'required|string',
            'country'                   => 'required|string|max:32',
        ];
    }

     
}
