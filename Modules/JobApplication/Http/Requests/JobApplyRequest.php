<?php

namespace Modules\JobApplication\Http\Requests;

use App\Rules\MobilePhoneBdRule;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class JobApplyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if(isApplicantUser()) {
            return [
                'job_code' => 'required',
                'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                'cover_letter' => 'sometimes|required|string',
                'answers' => 'nullable|array',
                'answers.*.question' => 'required|integer',
                'answers.*.answer' => 'nullable|string',
            ];

        } else {

            return [
                'job_code' => 'required',
                'email' => 'required|email',
                'first_name' => 'required|min:2|max:100',
                'last_name' => 'required|min:2|max:100',
                'country_code' => 'nullable|exists:countries,country_code',
                'mobile' => ['required', 'string', new MobilePhoneBdRule],
                'photo' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'cover_letter' => 'sometimes|required|string',
                'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
                'dob' => 'nullable|date',
                'gender' => 'nullable|string',
                'answers' => 'nullable|array',
                'answers.*.question' => 'required|integer',
                'answers.*.answer' => 'nullable|string',
            ];
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get validation failure messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'country_code' => 'Please provide a valid country code',
            'email' => 'Please provide a valid email address',
            'answers.array' => 'Please provide answers array',
            'resume.max' => 'Resume upload allowed maximum file size is 2MB',
            'resume' => 'Please provide valid resume file. Allowed types: pdf, doc, docx',
            'photo.max' => 'Photo upload allowed maximum file size is 2MB',
            'photo' => 'Please provide valid image. Allowed types: jpeg, png, jpg, gif',
            'cover_letter.required' => 'Please provide cover letter',
        ];
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
