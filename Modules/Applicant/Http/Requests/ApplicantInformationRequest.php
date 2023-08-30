<?php

namespace Modules\Applicant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Applicant\Utils\ProfileValidation;

class ApplicantInformationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'category' => 'required|string',
        ];
        if (request()->has('passing_year')) {
            $time = strtotime(request()->get('passing_year'));
            if ($time) {
                $this->merge([
                    'passing_year' => date('Y', $time),
                ]);
            }
        } elseif (request()->has('training_year')) {
            $time = strtotime(request()->get('training_year'));
            if ($time) {
                $this->merge([
                    'training_year' => date('Y', $time),
                ]);
            }
        }

        $this->manipulation();

        $rules = array_merge(
            $rules,
            ProfileValidation::getValidationRules(request()->get('category') ?? 'personal_information')
        );

        return $rules;
    }

    /**
     * Inject or manipulate the request data before validation.
     *
     * @return void
     */
    private function manipulation()
    {
        if (request()->has('passing_year')) {
            $time = strtotime(request()->get('passing_year'));
            if ($time) {
                $this->merge([
                    'passing_year' => date('Y', $time),
                ]);
            }
        } elseif (request()->has('training_year')) {
            $time = strtotime(request()->get('training_year'));
            if ($time) {
                $this->merge([
                    'training_year' => date('Y', $time),
                ]);
            }
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('sanctum')->check();
    }
}