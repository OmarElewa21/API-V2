<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Http\Requests\CreateBaseRequest;

class CreateCountryPartnerAssistantRequest extends CreateBaseRequest
{
    function __construct()
    {
        $this->key = 'country_partner_assistant';
        $this->unique_fields = ['username', 'email'];
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
     * @return arr of rules
     */
    protected function validationRules($key)
    {
        $rules = [
                $key.'.name'                => 'required|string|max:160',
                $key.'.role'                => 'required|string|in:country partner assistant',
                $key.'.username'            => ['required', 'string', 'max:64', Rule::unique('users', 'username')->whereNull('deleted_at')],
                $key.'.email'               => ['required', 'email', 'max:64', Rule::unique('users', 'email')->whereNull('deleted_at')],
                $key.'.password'            => ['required',
                                                Password::min(8)
                                                    ->letters()
                                                    ->numbers()
                                                    ->symbols()
                                                    ->uncompromised(), 'confirmed'],
                $key.'.country_id'          => 'required|exists:countries,id'
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
