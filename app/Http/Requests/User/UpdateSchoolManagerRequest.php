<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rules\Password;
use Illuminate\Routing\Route;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSchoolManagerRequest extends FormRequest
{
    /**
     * @var school_manager
     */
    private $school_manager;

    /**
     *
     * @param Route $route
     */
    function __construct(Route $route)
    {
        $this->school_manager = $route->parameter('school_manager');
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'                  => 'required|string|max:160',
            'username'              => ['required', 'string', 'max:64', Rule::unique('users')->ignore($this->school_manager)],
            'email'                 => ['required', 'email', 'max:64', Rule::unique('users')->ignore($this->school_manager)],
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
