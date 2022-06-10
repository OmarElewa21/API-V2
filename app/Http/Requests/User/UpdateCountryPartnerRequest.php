<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rules\Password;
use Illuminate\Routing\Route;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCountryPartnerRequest extends FormRequest
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

    /**
     * Overwrite validation return
     *
     * @return HttpResponseException
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
