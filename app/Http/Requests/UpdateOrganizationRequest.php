<?php

namespace App\Http\Requests;

use Illuminate\Routing\Route;
use Illuminate\Validation\Rule;

class UpdateOrganizationRequest extends CreateOrganizationRequest
{

    /**
     * @var organization
     */
    private $organization;

    /**
     *
     * @param Route $route
     */
    function __construct(Route $route)
    {
        $this->organization = $route->parameter('organization');
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'                      => ['required', 'string', 'max:164', Rule::unique('organizations')->ignore($this->organization)],
            'email'                     => ['required', 'email', 'max:164', Rule::unique('organizations')->ignore($this->organization)],
            'phone'                     => 'required|string|max:24',
            'person_in_charge_name'     => 'required|string|max:164',
            'address'                   => 'required|string',
            'billing_address'           => 'required|string',
            'shipping_address'          => 'required|string',
            'img'                       => 'required|string|max:255',
            'country_id'                => 'required|digits_between:2,251|exists:countries,id'
        ];
    }
}
