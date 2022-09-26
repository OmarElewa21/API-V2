<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Role;

class ChangeUserPermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole(['super admin', 'admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'all' => 'required|boolean'
        ];
        
        foreach(Role::permissin_set() as $key=>$set){
            $rules = array_merge($rules, [
                $key    => 'required|array'
            ]);

            foreach($set as $set_key=>$value){
                $rules = array_merge($rules, [
                    $key . '.' . $value  => 'required|boolean'
                ]);
            }
        }

        return $rules;
    }
}
