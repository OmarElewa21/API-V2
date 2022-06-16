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
        return [
            $key.'.name'                => 'required|string|max:132',
            $key.'.class'               => 'required|string|max:32',
            $key.'.grade'               => 'required|string|max:32',
            $key.'.user_id'             => ['required', Rule::exists('users', 'id')->whereNull('deleted_at')],
            $key.'.school_id'           => [Rule::exists('schools', 'id')->whereNull('deleted_at')],
            $key.'.tuition_centre_id'   => [Rule::exists('schools', 'id')->whereNull('deleted_at')->where('is_tuition_centre', 1)],
            $key.'.country_id'          => 'required|digits_between:2,251|exists:countries,id'
        ];
    }
}
