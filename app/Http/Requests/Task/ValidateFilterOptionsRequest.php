<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ValidateFilterOptionsRequest extends FormRequest
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
        $validation_arr = [
            'filterOptions'                     => 'array',
            'filterOptions.lang_count'          => 'integer',
            'filterOptions.status'              => 'string|in:pending,approved',
            'filterOptions.domains'             => 'array',
            'filterOptions.tags'                => 'array'
        ];
        
        $key = 'filterOptions';

        if(Arr::has($this->get($key), 'domains')){
            foreach($this->get($key)['domains'] as $k=>$domain){
                $validation_arr = array_merge($validation_arr, [
                    $key.'.domains.'.$k      => Rule::exists(\App\Models\DomainsTags::class, 'id')->where('is_tag', 0)->whereNull('deleted_at')
                ]);
            }
        }
        if(Arr::has($this->get($key), 'tags')){
            foreach($this->get($key)['tags'] as $k=>$domain){
                $validation_arr = array_merge($validation_arr, [
                    $key.'.tags.'.$k      => Rule::exists(\App\Models\DomainsTags::class, 'id')->where('is_tag', 1)->whereNull('deleted_at')
                ]);
            }
        }
        return $validation_arr;
    }
}
