<?php

namespace Modules\Applicant\Utils;

use App\Rules\MobilePhoneBdRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;
use Modules\Settings\Services\FormOptionService;

class ProfileValidation
{
    public static function getValidationRules(string $profileType): array
    {
        $validationRules = self::rules();

        return $validationRules[$profileType] ?? [];
    }

    /**
     * Get the validation rules that apply to the Application information update request.
     *
     * @return array
     */
    public static function rules(): array
    {
        $user = auth('sanctum')->user();

        return [
            'personal' => [
                'first_name' => 'required|string|max:100',
                'last_name' => 'nullable|string|max:100',
                'father_name' => 'required|string|max:100',
                'mother_name' => 'nullable|string|max:100',
                'dob' => 'required|date',
                'gender' => [
                    'required',
                    'string',
                    'in:' . implode(',', FormOptionService::getOptionArray('gender')),
                ],
                'religion' => [
                    'required',
                    'string',
                    'in:' . implode(',', FormOptionService::getOptionArray('religion')),
                ],
                'marital_status' => [
                    'required',
                    'string',
                    'in:' . implode(',', FormOptionService::getOptionArray('marriage')),
                ],
                'nationality' => 'required|string|max:50',
                'nid' => 'nullable|string|max:50',
                'primary_mobile' => [$user->mobile ? 'nullable' : 'required', 'string', new MobilePhoneBdRule],
                'secondary_mobile' => ['nullable', 'string', new MobilePhoneBdRule],
                'primary_email' => [$user->email ? 'nullable' : 'required', 'email'],
                'alternate_email' => ['nullable', 'email'],
                'height' => 'nullable|numeric|max:10',
                'weight' => 'nullable|numeric|max:200',
                'present_address' => 'required|string|max:255',
                'is_same_address' => 'sometimes|boolean',
                'permanent_address' => !isTrue(request()->is_same_address) ? 'required|string|max:255' : 'nullable',
                'present_salary' => 'nullable|string|max:50',
                'expected_salary' => 'nullable|string|max:50',
                'job_level' => [
                    'required',
                    'string',
                    'in:' . implode(',', FormOptionService::getOptionArray('job_level')),
                ],
                'job_type' => [
                    'required',
                    'string',
                    'in:' . implode(',', FormOptionService::getOptionArray('job_type')),
                ],
                'special_skills' => 'nullable|array',
                'special_skills.*' => 'integer',
                'preferred_functions' => 'sometimes|array',
                'preferred_functions.*' => 'integer',
                'career_objective' => 'required|string',
                'career_summary' => 'nullable|string',
                'special_qualification' => 'nullable|string',
                'has_disability' => 'sometimes|boolean',
                'disability_id' => 'nullable|string',
            ],
            'education' => [
                'id' => 'nullable|integer',
                'education_id' => 'required|integer|exists:educations,id',
                'major' => 'required|string|max:100',
                'degree' => 'required|string|max:100',
                'passing_year' => 'nullable|numeric|min:1900|max:' . date('Y'),
                'duration' => 'required|integer|min:1|max:10',
                'institute' => 'required|string|max:255',
                'achievement' => 'nullable|string|max:255',
                'board' => 'nullable|string|max:100',
                'result' => 'nullable|string|max:20',
                'mark' => 'nullable|string|max:20',
                'scale' => 'nullable|string|max:20',
            ],
            'training' => [
                'id' => 'nullable|integer',
                'title' => 'required|string|max:255',
                'country_id' => 'required|integer|exists:countries,id',
                'training_year' => 'nullable|numeric|min:1900|max:' . date('Y'),
                'topic' => 'nullable|string|max:255',
                'institute' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'duration' => 'required|string|max:20',
            ],
            'certification' => [
                'id' => 'nullable|integer',
                'certification' => 'required|string|max:255',
                'institute' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date' . (request()->start_date ? '|after:' . request()->start_date : ''),
            ],
            'experience' => [
                'is_fresher' => 'required|boolean',
                'id' => 'nullable|integer',
                'company_name' => 'required_if:is_fresher,0|string|max:255',
                'company_business' => 'nullable|string|max:255',
                'designation' => 'required_if:is_fresher,0|string|max:255',
                'department' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'start_date' => 'required_if:is_fresher,0|date',
                'end_date' => 'nullable|date' . (request()->start_date ? '|after:' . request()->start_date : ''),
                'is_current' => (request()->is_fresher == 0 ? 'sometimes' : 'nullable') . '|boolean',
                'responsibility' => 'nullable|string',
                'expertise' => 'nullable|array',
                'expertise.*.name' => 'required|string|max:100',
                'expertise.*.year' => 'required|integer',
                'expertise.*.month' => 'required|integer',
            ],
            'specialization' => [
                'skill_description' => 'sometimes|string',
                'skills' => 'required|array',
                'skills.*.skill' => 'required|string',
                'skills.*.learn_by' => 'required|string',
            ],
            'language' => [
                'id' => 'nullable|integer',
                'language' => 'required|string|max:50',
                'reading' => 'sometimes|string',
                'writing' => 'sometimes|string',
                'speaking' => 'sometimes|string',
            ],
            'reference' => [
                'id' => 'nullable|integer',
                'name' => 'required|string|max:100',
                'organization' => 'required|string|max:255',
                'designation' => 'required|string|max:255',
                'mobile' => ['required', 'string', new MobilePhoneBdRule],
                'email' => 'required|email|max:100',
                'address' => 'nullable|string|max:255',
                'phone_office' => 'nullable|string|max:20',
                'phone_home' => 'nullable|string|max:20',
                'relation' => 'nullable|string|max:100',
            ],
        ];
    }
}