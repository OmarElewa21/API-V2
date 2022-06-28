<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rules\Password;
use Illuminate\Routing\Route;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCountryPartnerAssistantRequest extends FormRequest
{
    /**
     * @var cp_assistant
     */
    private $cp_assistant;

    /**
     *
     * @param Route $route
     */
    function __construct(Route $route)
    {
        $this->cp_assistant = $route->parameter('country_partner_assistant');
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole(['super admin', 'admin', 'country partner']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'                  => 'required|string|max:160',
            'username'              => ['required', 'string', 'max:64', Rule::unique('users')->ignore($this->cp_assistant)],
            'email'                 => ['required', 'email', 'max:64', Rule::unique('users')->ignore($this->cp_assistant)],
            'password'              => ['required',
                                        Password::min(8)
                                            ->letters()
                                            ->numbers()
                                            ->symbols()
                                            ->uncompromised(), 'confirmed'],
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
