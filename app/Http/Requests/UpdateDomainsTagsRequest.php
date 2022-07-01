<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateDomainsTagsRequest extends FormRequest
{
    /**
     * @var domain_tag
     */
    private $domain_tag;

    /**
     *
     * @param Route $route
     */
    function __construct(Route $route)
    {
        $this->domain_tag = $route->parameter('domain');
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole('super admin') || auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        if(!$this->domain_tag->is_tag){
            return [
                'name'            => ['required', 'string', 'max:132', Rule::unique('domains_tags')->where('is_tag', 0)->ignore($this->domain_tag)],
                'topics'          => 'array',
            ];
        }else{
            return [
                'name'            => ['required', 'string', 'max:132', Rule::unique('domains_tags')->where('is_tag', 1)->ignore($this->domain_tag)],
            ];
        }
        
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
