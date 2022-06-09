<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Route;

class UpdateCountryPartnerRequest extends CreateCountryPartnerRequest
{
    /**
     * @var country_partner
     */
    private $country_partner;

    /**
     *
     * @param Route $route
     */
    function __construct(Route $route)
    {
        $this->country_partner = $route->parameter('country_partner');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'              => 'required|string|max:160',
            'role'              => 'required|exists:roles,name',
            'username'          => ['required', 'string', 'max:64', Rule::unique('users')->ignore($this->country_partner->user)],
            'email'             => ['required', 'email', 'max:64', Rule::unique('users')->ignore($this->country_partner->user)],
            'password'          => ['required',
                                    Password::min(8)
                                        ->letters()
                                        ->numbers()
                                        ->symbols()
                                        ->uncompromised(), 'confirmed'],
            'organization'      => 'required|exists:organizations,name',
            'country_id'        => 'required|digits_between:2,251|exists:countries,id'
        ];
    }
}
