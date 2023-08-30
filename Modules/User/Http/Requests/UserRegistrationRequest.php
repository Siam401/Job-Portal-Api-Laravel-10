<?php

namespace Modules\User\Http\Requests;

use App\Rules\MobilePhoneBdRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UserRegistrationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:100|min:2',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|string|unique:users,email|max:255',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|same:password',
            // 'country_code' => 'nullable|exists:countries,country_code',
            'mobile' => ['required', 'string', new MobilePhoneBdRule, 'unique:users,mobile'],
            'photo' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender' => 'required|string',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !auth('sanctum')->check();
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws HttpResponseException
     */
    public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'result_code' => 10,
                'message' => $validator->errors()->first()
            ])
        );
    }
}
