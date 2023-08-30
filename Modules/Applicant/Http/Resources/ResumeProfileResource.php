<?php

namespace Modules\Applicant\Http\Resources;

use App\Services\FileUpload\FileUpload;
use Faker\Core\File;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Applicant\Models\ApplicantExperience;
use Modules\Job\Models\JobFunction;
use Modules\Location\Models\Country;
use Modules\Settings\Services\FormOptionService;

class ResumeProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        // ----------------- Personal Information -----------------
        $resume = [
            'basic' => [
                'name' => trim($this->first_name . ' ' . $this->last_name),
                'address' => $this->personalInformation?->present_address,
                'primary_mobile' => $this->mobile,
                'secondary_mobile' => $this->personalInformation?->secondary_mobile,
                'email' => $this->email,
                'photo' => $this->photo ? FileUpload::getUrl($this->photo) : null,
                'career_objective' => $this->personalInformation?->career_objective,
                'career_summary' => $this->personalInformation?->career_summary,
                'special_qualification' => $this->personalInformation?->special_qualification,
            ],
            'personal' => [
                'father_name' => $this->personalInformation?->father_name,
                'mother_name' => $this->personalInformation?->mother_name,
                'dob' => $this->dob ? date('M j, Y', strtotime($this->dob)) : null,
                'gender' => FormOptionService::getOptionTitle('gender', $this->gender),
                'height' => $this->personalInformation?->height,
                'weight' => $this->personalInformation?->weight,
                'marital_status' => FormOptionService::getOptionTitle('marriage', $this->personalInformation?->marital_status),
                'nationality' => $this->personalInformation?->nationality,
                'nid' => $this->personalInformation?->nid,
                'religion' => FormOptionService::getOptionTitle('religion', $this->personalInformation?->religion),
                'present_address' => $this->personalInformation?->present_address,
                'permanent_address' => isTrue($this->personalInformation?->is_same_address)
                ? $this->personalInformation?->present_address
                : $this->personalInformation?->permanent_address,
                'disability_id' => $this->personalInformation?->disability_id,
            ]
        ];


        // ----------------- Employment History -----------------
        $experiences = ApplicantExperience::prepareResponse($this->experiences);
        $resume = array_merge($resume, $experiences);


        // ----------------- Academic Qualifications -----------------
        $resume['educations'] = $this->educations->map(function ($education) {
            return [
                'degree' => $education->degree,
                'major' => $education->major,
                'institute' => $education->institute,
                'result' => $education->mark,
                'passing_year' => $education->passing_year,
                'duration' => $education->duration,
                'achievement' => $education->achievement,
            ];
        });


        // ----------------- Trainings & Certifications -----------------
        $resume['trainings'] = $this->trainings->map(function ($training) {
            return [
                'title' => $training->title,
                'topic' => $training->topic,
                'institute' => $training->institute,
                'country' => Country::find($training->country_id)?->name,
                'duration' => $training->duration,
                'location' => $training->location,
                'year' => $training->training_year,
            ];
        });
        $resume['certifications'] = $this->certifications->map(function ($certification) {
            return [
                'title' => $certification->certification,
                'institute' => $certification->institute,
                'location' => $certification->location,
                'from_date' => date('F j, Y', strtotime($certification->start_date)),
                'to_date' => date('F j, Y', strtotime($certification->end_date)),
            ];
        });

        // ----------------- Career Information -----------------
        $resume['career'] = [
            'job_level' => $this->personalInformation?->job_level,
            'job_type' => $this->personalInformation?->job_type,
            'present_salary' => $this->personalInformation?->present_salary,
            'expected_salary' => $this->personalInformation?->expected_salary,
            'preferred_functions' => JobFunction::getGluedFunctions($this->personalInformation?->preferred_functions),
        ];


        // ----------------- Specialization -----------------
        $metas = $this->metas?->pluck('key_value', 'key_name');
        $resume['specialization'] = [
            'fields' => $metas ? json_decode($metas->get('skills'), true) : [],
            'description' => $metas ? $metas->get('skill_description') : '',
        ];


        // ----------------- Language & Reference -----------------
        $resume['languages'] = $this->languages->map(function ($language) {
            return $language->setHidden([
                'applicant_id', 'created_at', 'updated_at', 'id'
            ])->toArray();
        })->toArray();
        $resume['references'] = $this->references->map(function ($reference) {
            return $reference->setHidden([
                'applicant_id', 'created_at', 'updated_at', 'id', 'sequence'
            ])->toArray();
        })->toArray();

        return $resume;
    }
}