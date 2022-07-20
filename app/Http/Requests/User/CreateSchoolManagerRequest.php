<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Http\Requests\CreateBaseRequest;

class CreateSchoolManagerRequest extends CreateBaseRequest
{
    function __construct()
    {
        $this->key = 'school_manager';
        $this->unique_fields = ['username', 'email'];
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
     * @return arr of rules
     */
    protected function validationRules($key)
    {
        $rules = [
            $key.'.name'                => 'required|string|max:160',
            $key.'.username'            => ['required', 'string', 'max:64', Rule::unique('users', 'username')->whereNull('deleted_at')],
            $key.'.email'               => ['required', 'email', 'max:64', Rule::unique('users', 'email')->whereNull('deleted_at')],
            $key.'.password'            => ['required',
                                            Password::min(8)
                                                ->letters()
                                                ->numbers()
                                                ->symbols()
                                                ->uncompromised(), 'confirmed'],
            $key.'.country_partner_id'  => ['required', Rule::exists('country_partners', 'user_id')],
            $key.'.school_id'           => ['required', Rule::exists('schools', 'id')->whereNull('deleted_at')]
        ];

        if(auth()->user()->hasRole(['admin', 'super admin'])){
            $rules = array_merge($rules, [
                    $key.'.country_partner_id' => ['required',
                                                    Rule::exists('users', 'id')->where(function($query){
                                                        $query->join('roles', function ($join) {
                                                            $join->on('roles.id', '=', 'users.role_id')->where('roles.name', 'country partner');
                                                        })->whereNull('deleted_at');
                                                    })
                                                    ]
                                                                
                ]
            );
        }
        return $rules; 
    }
}
