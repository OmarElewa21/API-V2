<?php

namespace App\Http\Requests;

class SaveRoleRequest extends BaseRequest
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
            'name'              => 'required|string|max:32',
            'permission_set'    => 'required|array'
        ];
    }
}
