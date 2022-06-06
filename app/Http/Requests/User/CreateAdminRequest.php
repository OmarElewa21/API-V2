<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;

class CreateAdminRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole('super admin');
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
            'username'          => ['required', 'string', 'max:64', Rule::unique('users')->whereNull('deleted_at')],
            'email'             => ['required', 'email', 'max:64', Rule::unique('users')->whereNull('deleted_at')],
            'password'          => Password::min(8)
                                        ->letters()
                                        ->numbers()
                                        ->symbols()
                                        ->uncompromised()
        ];
    }
}
