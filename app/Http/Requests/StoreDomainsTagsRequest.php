<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Route;

class StoreDomainsTagsRequest extends CreateBaseRequest
{
    function __construct()
    {
        parent::__construct();
        $this->key = Route::currentRouteName() === 'domains.store' ? 'domain' : 'tag';
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
            $key.'.topics'          => 'array',
        ];
    }
}
