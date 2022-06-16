<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class UpdateParticipantRequest extends FormRequest
{
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'                => 'required|string|max:132',
            'class'               => 'required|string|max:32',
            'grade'               => 'required|string|max:32',
            'user_id'             => ['required', Rule::exists('users', 'id')->whereNull('deleted_at')],
            'school_id'           => [Rule::exists('schools', 'id')->whereNull('deleted_at')->where('is_tuition_centre', 0)],
            'tuition_centre_id'   => [Rule::exists('schools', 'id')->whereNull('deleted_at')->where('is_tuition_centre', 1)],
            'country_id'          => 'required|digits_between:2,251|exists:countries,id',
            'password'            => ['required',
                                        Password::min(8)
                                            ->letters()
                                            ->numbers()
                                            ->uncompromised(), 'confirmed'],
        ];
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
