<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator as Validate;

use Illuminate\Support\Str;

class CreateBaseRequest extends FormRequest
{
    protected $key = '';
    protected $unique_fields = [];

    /**
     * Overwrite validation return
     *
     * @return HttpResponseException
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach($this->all() as $key=>$data){
                $validateKey = explode(" ", $key);
                if(Str::lower($validateKey[0]) !== $this->key || !is_numeric($validateKey[1]) || count($validateKey) > 2){
                    $validator->errors()->add($this->key, 'key '.$key.' is not valid');
                }
                $this->checkUniqueness($validator);
                Validate::validate($this->all(), $this->validationRules($key));
            }
        });
    }

    /**
     * 
     * @return arr of rules
     */
    protected function validationRules($key)
    {
        return [];
    }


    protected function checkUniqueness($validator){
        foreach($this->unique_fields as $field_check){
            $list_check = [];
            foreach($this->all() as $data){
                $list_check[] = $data[$field_check];
            }
            if(count($list_check) > count(array_unique($list_check))){
                $validator->errors()->add($this->key, $field_check . ' has duplicates, please review ' . $field_check . ' field across you submitted data');
            }
        }
    }
}
