<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Http\Requests\CreateBaseRequest;

class CreateTeacherRequest extends CreateBaseRequest
{
    function __construct()
    {
        $this->key = 'teacher';
        $this->unique_fields = ['username', 'email'];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole(['super admin', 'admin', 'country partner', 'country partner assistant', 'school manager']);
    }

    /**
     * @return arr of rules
     */
    protected function validationRules($key)
    {
        return [
            $key.'.name'                => 'required|string|max:160',
            $key.'.role'                => 'required|string|in:teacher',
            $key.'.username'            => ['required', 'string', 'max:64', Rule::unique('users', 'username')->whereNull('deleted_at')],
            $key.'.email'               => ['required', 'email', 'max:64', Rule::unique('users', 'email')->whereNull('deleted_at')],
            $key.'.password'            => ['required',
                                            Password::min(8)
                                                ->letters()
                                                ->numbers()
                                                ->symbols()
                                                ->uncompromised(), 'confirmed'],
            $key.'.country_partner_id'  => ['required', Rule::exists('country_partners', 'user_id')],
            $key.'.school_id'           => ['required', Rule::exists('schools', 'id')->whereNull('deleted_at')],
            $key.'.country_id'          => 'required|exists:countries,id'
        ];
    }
}
