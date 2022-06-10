<?php

namespace App\Http\Requests;

use Illuminate\Routing\Route;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOrganizationRequest extends FormRequest
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

    /**
     * Overwrite validation return
     *
     * @return HttpResponseException
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
