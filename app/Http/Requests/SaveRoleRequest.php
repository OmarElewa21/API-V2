<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Role;

class SaveRoleRequest extends FormRequest
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
            'name'                          => 'required|string|max:32',
            'description'                   => 'string',
            'permission_set'                => 'required|array',
            'permission_set.all'            => 'required|boolean'
        ];

        foreach(Role::permissin_set() as $key=>$set){
            $rules = array_merge($rules, [
                'permission_set.' . $key    => 'required|array'
            ]);

            foreach($set as $set_key=>$value){
                $rules = array_merge($rules, [
                    'permission_set.' . $key . '.' . $value  => 'required|boolean'
                ]);
            }
        }

        return $rules;
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
