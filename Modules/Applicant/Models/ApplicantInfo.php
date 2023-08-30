<?php

namespace Modules\Applicant\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\Applicant;

class ApplicantInfo extends Model
{
    protected $fillable = [
        'applicant_id',
        'father_name',
        'mother_name',
        'religion',
        'marital_status',
        'nationality',
        'nid',
        'secondary_mobile',
        'alternate_email',
        'height',
        'weight',
        'present_address',
        'is_same_address',
        'permanent_address',
        'career_objective',
        'present_salary',
        'expected_salary',
        'job_level',
        'job_type',
        'preferred_functions',
        'special_skills',
        'career_summary',
        'special_qualification',
        'has_disability',
        'disability_id',
    ];

    protected $casts = [
        'is_same_address' => 'boolean',
        'has_disability' => 'boolean',
        'preferred_functions' => 'array',
        'special_skills' => 'array',
    ];

    public static function getDefault(Applicant $applicant)
    {
        return [
            "father_name" => null,
            "mother_name" => null,
            "religion" => null,
            "marital_status" => null,
            "nationality" => null,
            "nid" => null,
            "secondary_countrycode" => null,
            "secondary_mobile" => null,
            "alternate_email" => null,
            "height" => 0,
            "weight" => 0,
            "present_address" => "",
            "is_same_address" => false,
            "permanent_address" => "",
            "career_objective" => null,
            "present_salary" => null,
            "expected_salary" => null,
            "job_level" => "mid",
            "job_type" => "full_time",
            "preferred_functions" => [],
            "special_skills" => [],
            "career_summary" => null,
            "special_qualification" => null,
            "has_disability" => false,
            "disability_id" => null,
            'primary_email' => $applicant->email,
            'primary_mobile' => $applicant->mobile,
            'first_name' => $applicant->first_name,
            'last_name' => $applicant->last_name,
            'dob' => $applicant->dob,
            'gender' => $applicant->gender,
        ];
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}