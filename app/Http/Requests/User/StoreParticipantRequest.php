<?php

namespace App\Http\Requests\User;

use App\Http\Requests\CreateBaseRequest;
use Illuminate\Validation\Rule;

class StoreParticipantRequest extends CreateBaseRequest
{
    function __construct()
    {
        $this->key = 'participant';
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole(['super admin', 'admin', 'country partner', 'country partner assistant', 'school manager', 'teacher']);
    }

    /**
     * @return arr of rules
     */
    protected function validationRules($key)
    {
        $rules = [
            $key.'.name'                => 'required|string|max:132',
            $key.'.class'               => 'required|string|max:32',
            $key.'.grade'               => 'required|string|max:32',
            $key.'.competition_id'      => ['required', Rule::exists('competitions', 'id')->whereNull('deleted_at')],
            $key.'.tuition_centre_id'   => [Rule::exists('schools', 'id')->whereNull('deleted_at')->where('is_tuition_centre', 1)]
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

        if(!auth()->user()->hasRole(['school manager', 'teacher'])){
            $rules = array_merge($rules, [
                $key.'.school_id'           => ['required', Rule::exists('schools', 'id')->whereNull('deleted_at')]
            ]);
        }
        
        return $rules; 
    }
}
