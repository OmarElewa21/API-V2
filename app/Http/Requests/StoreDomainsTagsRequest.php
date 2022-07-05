<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDomainsTagsRequest extends CreateBaseRequest
{
    function __construct()
    {
        $this->key = 'domains_tags';
        $this->unique_fields = ['name'];
    }

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
     * @return arr of rules
     */
    protected function validationRules($key)
    {
        return [
            $key.'.name'            => 'required|string|max:132',
            $key.'.is_tag'          => 'required|boolean',
            $key.'.topics'          => 'array',
        ];
    }
}
