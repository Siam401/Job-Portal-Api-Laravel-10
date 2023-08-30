<?php

namespace Modules\Applicant\Services;

use App\Services\FileUpload\FileUpload;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Applicant\Models\ApplicantCertification;
use Modules\Applicant\Models\ApplicantEducation;
use Modules\Applicant\Models\ApplicantExperience;
use Modules\Applicant\Models\ApplicantInfo;
use Modules\Applicant\Models\ApplicantLanguage;
use Modules\Applicant\Models\ApplicantMeta;
use Modules\Applicant\Models\ApplicantReference;
use Modules\Applicant\Models\ApplicantTraining;
use Modules\User\Models\Applicant;

class ApplicantInformationService
{

    public function __construct(protected Applicant $applicant, public string|null $error = null)
    {
        // auth('sanctum')->user()?->applicant;
    }

    /**
     * Update applicant information
     *
     * @param array $data
     * @return void
     */
    public function updateApplicant(array $data): void
    {

        $user = $this->applicant->user;

        $this->applicant->email = $user->email ?? $data['primary_email'];
        $this->applicant->mobile = ($user->mobile !== $data['primary_mobile']) ? $data['primary_mobile'] : $user->mobile;
        $this->applicant->first_name = $data['first_name'];
        $this->applicant->last_name = $data['last_name'] ?? null;
        $this->applicant->dob = $data['dob'] ?? null;
        $this->applicant->gender = $data['gender'];

        $this->applicant->save();

        if ($user && $user->id) {
            if (empty($user->email)) {
                $user->email = $this->applicant->email;
            }
            $user->mobile = ($user->mobile !== $this->applicant->mobile) ? $this->applicant->mobile : $user->mobile;
            $user->name = trim($this->applicant->first_name . ' ' . $this->applicant->last_name);
            $user->save();
        }

    }

    /**
     * Get applicant personal information
     *
     * @return array
     */
    public function getPersonalInformation(): array
    {
        $applicantInfo = $this->applicant->personalInformation()->first();
        if (empty($applicantInfo)) {
            return ApplicantInfo::getDefault($this->applicant);
        }

        $applicantInfo->preferred_functions = $applicantInfo->preferred_functions ? array_map('intval', $applicantInfo->preferred_functions) : [];
        $applicantInfo->special_skills = $applicantInfo->special_skills ? array_map('intval', $applicantInfo->special_skills) : [];

        $user = $this->applicant->user;

        $applicantInfo->primary_email = empty($user->email) ? $this->applicant->email : $user->email;
        $applicantInfo->primary_mobile = empty($user->mobile) ? $this->applicant->mobile : $user->mobile;
        $applicantInfo->first_name = $this->applicant->first_name;
        $applicantInfo->last_name = $this->applicant->last_name;
        $applicantInfo->dob = $this->applicant->dob;
        $applicantInfo->gender = $this->applicant->gender;

        return $applicantInfo->setHidden([
            'id', 'applicant_id', 'created_at', 'updated_at'
        ])->toArray();
    }

    /**
     * Save applicant personal information
     *
     * @param array $data
     * @return bool|array
     */
    public function savePersonalInformation(array $data): bool|array
    {
        DB::beginTransaction();

        try {
            // Save applicant table data
            $this->updateApplicant($data);

            // Save applicant personal information table data
            $personalInformation = $this->applicant->personalInformation()->first();

            $data['permanent_address'] = $data['is_same_address'] ? $data['present_address'] : $data['permanent_address'];

            $data = collect($data)->only(
                'father_name', 'mother_name', 'religion', 'marital_status',
                'nationality', 'nid', 'secondary_mobile', 'alternate_email',
                'height', 'weight', 'present_address', 'is_same_address', 'permanent_address',
                'present_salary', 'expected_salary', 'job_level', 'job_type', 'career_objective',
                'special_skills', 'preferred_functions', 'career_summary', 'special_qualification',
                'has_disability', 'disability_id'
            )->toArray();


            if (!$personalInformation) {
                $personalInformation = $this->applicant->personalInformation()->create($data);
            } else {
                $personalInformation->update($data);
            }

            $this->applicant->updateProfileResumeStatus('personal');

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            $this->error = $e->getMessage();

            return false;
        }

        return $this->getPersonalInformation();
    }

    /**
     * Get applicant academic information
     *
     * @return array
     */
    public function getAcademicInformation(ApplicantEducation $applicantEducation = null): array
    {
        if ($applicantEducation && $applicantEducation->id) {
            return $applicantEducation->setHidden([
                'applicant_id', 'created_at', 'updated_at'
            ])->toArray();
        }

        $applicantEducation = $this->applicant->educations()->get();

        if (empty($applicantEducation)) {
            return [];
        }

        return $applicantEducation->map(function ($education) {
            return $education->setHidden([
                'applicant_id', 'created_at', 'updated_at'
            ])->toArray();
        })->toArray();
    }

    /**
     * Save applicant academic information
     *
     * @param array $data
     * @return boolean|array
     */
    public function saveAcademicInformation(array $data): bool|array
    {
        $applicantEducation = new ApplicantEducation();

        try {
            if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
                $applicantEducation = $applicantEducation->find($data['id']);
                if (!$applicantEducation || $applicantEducation->applicant_id != $this->applicant->id) {
                    throw new Exception('Unauthorized attempt to update applicant education information');
                }
            } else {
                // if($applicantEducation->where('applicant_id', $this->applicant->id)->where('')->exists()) {
                //     throw new Exception('Unauthorized attempt to update applicant education information');
                // }

                $applicantEducation->applicant_id = $this->applicant->id;
            }

            $applicantEducation->education_id = $data['education_id'];
            $applicantEducation->major = $data['major'];
            $applicantEducation->degree = $data['degree'];
            $applicantEducation->passing_year = $data['passing_year'] ?? null;
            $applicantEducation->duration = $data['duration'];
            $applicantEducation->institute = $data['institute'];
            $applicantEducation->achievement = $data['achievement'] ?? null;
            $applicantEducation->board = $data['board'] ?? null;
            $applicantEducation->result = $data['result'] ?? null;
            $applicantEducation->mark = $data['mark'] ?? null;
            $applicantEducation->scale = $data['scale'] ?? null;
            $applicantEducation->save();

            $this->applicant->updateProfileResumeStatus('education');
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

        return $this->getAcademicInformation($applicantEducation);
    }

    /**
     * Get applicant certification information
     *
     * @return array
     */
    public function getCertificationInformation(ApplicantCertification $applicantCert = null): array
    {
        if ($applicantCert && $applicantCert->id) {
            return $applicantCert->setHidden([
                'applicant_id', 'created_at', 'updated_at'
            ])->toArray();
        }

        $applicantCert = $this->applicant->certifications()->get();

        if (empty($applicantCert)) {
            return [];
        }

        return $applicantCert->map(function ($education) {
            return $education->setHidden([
                'applicant_id', 'created_at', 'updated_at'
            ])->toArray();
        })->toArray();
    }

    /**
     * Save applicant certification information
     *
     * @param array $data
     * @return boolean|array
     */
    public function saveCertificationInformation(array $data): bool|array
    {
        $applicantCert = new ApplicantCertification();

        try {
            if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
                $applicantCert = $applicantCert->find($data['id']);
                if (!$applicantCert || $applicantCert->applicant_id != $this->applicant->id) {
                    throw new Exception('Unauthorized attempt to update applicant certification information');
                }
            } else {
                if ($applicantCert->where('applicant_id', $this->applicant->id)->where('certification', $data['certification'])->exists()) {
                    throw new Exception('Following Certification Information already exists for the Applicant');
                }

                $applicantCert->applicant_id = $this->applicant->id;
            }

            $applicantCert->certification = $data['certification'];
            $applicantCert->institute = $data['institute'];
            $applicantCert->location = $data['location'];
            $applicantCert->start_date = $data['start_date'] ?? null;
            $applicantCert->end_date = $data['end_date'] ?? null;
            $applicantCert->save();

        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

        return $this->getCertificationInformation($applicantCert);
    }

    /**
     * Get applicant training information
     *
     * @return array
     */
    public function getTrainingInformation(ApplicantTraining $applicantTraining = null): array
    {
        if ($applicantTraining && $applicantTraining->id) {
            return $applicantTraining->setHidden([
                'applicant_id', 'created_at', 'updated_at'
            ])->toArray();
        }

        $applicantTraining = $this->applicant->trainings()->get();

        if (empty($applicantTraining) || $applicantTraining->isEmpty()) {
            return [];
        }

        return $applicantTraining->map(function ($education) {
            return $education->setHidden([
                'applicant_id', 'created_at', 'updated_at'
            ])->toArray();
        })->toArray();
    }

    /**
     * Save applicant training information
     *
     * @param array $data
     * @return boolean|array
     */
    public function saveTrainingInformation(array $data): bool|array
    {
        $applicantTraining = new ApplicantTraining();

        try {
            if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
                $applicantTraining = $applicantTraining->find($data['id']);
                if (!$applicantTraining || $applicantTraining->applicant_id != $this->applicant->id) {
                    throw new Exception('Unauthorized attempt to update applicant training information');
                }
            } else {
                if ($applicantTraining->where('applicant_id', $this->applicant->id)->where('title', $data['title'])->exists()) {
                    throw new Exception('Training title already exists in Applicant Training Information');
                }

                $applicantTraining->applicant_id = $this->applicant->id;
            }

            $applicantTraining->title = $data['title'];
            $applicantTraining->country_id = $data['country_id'];
            $applicantTraining->training_year = $data['training_year'] ?? null;
            $applicantTraining->topic = $data['topic'] ?? null;
            $applicantTraining->institute = $data['institute'];
            $applicantTraining->location = $data['location'];
            $applicantTraining->duration = $data['duration'];
            $applicantTraining->save();

            // $this->applicant->updateProfileResumeStatus('', true);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

        return $this->getTrainingInformation($applicantTraining);
    }

    /**
     * Get applicant experience information
     *
     * @return array
     */
    public function getEmploymentHistory(ApplicantExperience $applicantExperience = null): array
    {

        if ($applicantExperience && $applicantExperience->id) {
            return $applicantExperience->setHidden([
                'applicant_id', 'created_at', 'updated_at'
            ])->toArray();
        }

        $applicantMeta = ApplicantMeta::where([
            'applicant_id' => $this->applicant->id,
            'key_name' => 'is_fresher'
        ])->first();

        $isFresher = false;
        $applicantExperience = [];
        if ($applicantMeta && $applicantMeta->key_value == 1) {
            $isFresher = true;
        } else {
            $applicantExperience = $this->applicant->experiences()->get();
        }

        // if (empty($applicantExperience) || $applicantExperience->isEmpty()) {
        // }
        return [
            'is_fresher' => $isFresher,
            'experience' => $applicantExperience ? $applicantExperience->map(function ($experience) {
                return $experience->setHidden([
                    'applicant_id', 'created_at', 'updated_at'
                ])->toArray();
            })->toArray() : []
        ];
    }

    /**
     * Save applicant employment history
     *
     * @param array $data
     * @return boolean|array
     */
    public function saveEmploymentHistory(array $data): bool|array
    {

        // Update if applicant is fresher
        $isFresher = $data['is_fresher'] ?? false;
        $applicantMeta = ApplicantMeta::firstOrNew([
            'applicant_id' => $this->applicant->id,
            'key_name' => 'is_fresher'
        ]);
        $applicantMeta->key_value = isTrue($isFresher) ? 1 : 0;
        $applicantMeta->save();

        if (isTrue($isFresher)) {
            return $this->getEmploymentHistory();
        }

        // Save applicant employment history if not a fresher
        $applicantExperience = new ApplicantExperience();

        try {
            if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
                $applicantExperience = $applicantExperience->find($data['id']);
                if (!$applicantExperience || $applicantExperience->applicant_id != $this->applicant->id) {
                    throw new Exception('Unauthorized attempt to update applicant employment history');
                }
            } else {

                $applicantExperience->applicant_id = $this->applicant->id;
            }

            $applicantExperience->company_name = $data['company_name'];
            $applicantExperience->company_business = $data['company_business'] ?? null;
            $applicantExperience->designation = $data['designation'];
            $applicantExperience->department = $data['department'] ?? null;
            $applicantExperience->location = $data['location'] ?? null;
            $applicantExperience->start_date = $data['start_date'];
            $applicantExperience->is_current = $data['is_current'] ?? false;
            $applicantExperience->end_date = $applicantExperience->is_current ? null : ($data['end_date'] ?? null);
            $applicantExperience->responsibility = $data['responsibility'] ?? null;
            $applicantExperience->expertise = $data['expertise'] ?? [];
            $applicantExperience->save();

            $this->applicant->updateProfileResumeStatus('experience');
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

        return $this->getEmploymentHistory($applicantExperience);
    }

    /**
     * Get applicant training information
     *
     * @return array
     */
    public function getSpecializationInformation(): array
    {
        $result = [
            'skill_description' => '',
            'skills' => []
        ];

        $spcData = ApplicantMeta::where('applicant_id', $this->applicant->id)
            ->whereIn('key_name', array_keys($result))
            ->get();

        if ($spcData->count() <= 0) {
            return $result;
        }

        foreach ($spcData as $data) {
            $result[$data->key_name] = $data->key_name === 'skills' ? json_decode($data->key_value, true) : $data->key_value;
        }

        return $result;
    }

    /**
     * Save applicant specialization information
     *
     * @param array $data
     * @return boolean|array
     */
    public function saveSpecializationInformation(array $data): bool|array
    {
        try {

            if (isset($data['skill_description'])) {
                $applicantMeta = ApplicantMeta::firstOrNew([
                    'applicant_id' => $this->applicant->id,
                    'key_name' => 'skill_description'
                ]);
                $applicantMeta->key_value = $data['skill_description'];
                $applicantMeta->save();
            }

            if (isset($data['skills']) && is_array($data['skills'])) {
                $applicantMeta = ApplicantMeta::firstOrNew([
                    'applicant_id' => $this->applicant->id,
                    'key_name' => 'skills'
                ]);
                $applicantMeta->key_value = json_encode($data['skills']);
                $applicantMeta->save();
            }

            $this->applicant->updateProfileResumeStatus('skill');
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

        return $this->getSpecializationInformation();
    }

    /**
     * Get applicant language information
     *
     * @return array
     */
    public function getLanguageInformation(ApplicantLanguage $applicantLanguage = null): array
    {

        if ($applicantLanguage && $applicantLanguage->id) {
            return $applicantLanguage->setHidden([
                'applicant_id', 'created_at', 'updated_at'
            ])->toArray();
        }

        $applicantLang = $this->applicant->languages()->get();

        if (empty($applicantLang) || $applicantLang->isEmpty()) {
            return [];
        }

        return $applicantLang->map(function ($lang) {
            return $lang->setHidden([
                'applicant_id', 'created_at', 'updated_at'
            ])->toArray();
        })->toArray();
    }

    /**
     * Save applicant language information
     *
     * @param array $data
     * @return boolean|array
     */
    public function saveLanguageInformation(array $data): bool|array
    {
        $applicantLang = new ApplicantLanguage();

        try {
            if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
                $applicantLang = $applicantLang->find($data['id']);
                if (!$applicantLang || $applicantLang->applicant_id != $this->applicant->id) {
                    throw new Exception('Unauthorized attempt to update applicant language information');
                }
            } else {
                if ($applicantLang->where('applicant_id', $this->applicant->id)->where('language', $data['language'])->exists()) {
                    throw new Exception($data['language'] . ' already exists in Applicant Language Information');
                }

                $applicantLang->applicant_id = $this->applicant->id;
            }

            $applicantLang->language = $data['language'];
            $applicantLang->reading = $data['reading'] ?? null;
            $applicantLang->writing = $data['writing'] ?? null;
            $applicantLang->speaking = $data['speaking'] ?? null;
            $applicantLang->save();

            $this->applicant->updateProfileResumeStatus('language');
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

        return $this->getLanguageInformation($applicantLang);
    }

    /**
     * Get applicant reference information
     *
     * @return array
     */
    public function getReferenceInformation(ApplicantReference $applicantRef = null): array
    {

        if ($applicantRef && $applicantRef->id) {
            return $applicantRef->setHidden([
                'applicant_id', 'created_at', 'updated_at'
            ])->toArray();
        }

        $applicantRef = $this->applicant->references()->get();

        if (empty($applicantRef) || $applicantRef->isEmpty()) {
            return [];
        }

        return $applicantRef->map(function ($lang) {
            return $lang->setHidden([
                'applicant_id', 'created_at', 'updated_at'
            ])->toArray();
        })->toArray();
    }

    /**
     * Save applicant reference information
     *
     * @param array $data
     * @return boolean|array
     */
    public function saveReferenceInformation(array $data): bool|array
    {
        $applicantRef = new ApplicantReference();

        try {
            if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
                $applicantRef = $applicantRef->find($data['id']);
                if (!$applicantRef || $applicantRef->applicant_id != $this->applicant->id) {
                    throw new Exception('Unauthorized attempt to update applicant reference information');
                }
            } else {
                if ($applicantRef->where([
                    'applicant_id' => $this->applicant->id,
                    'name' => $data['name'],
                    'mobile' => $data['mobile'],
                ])->exists()) {
                    throw new Exception($data['name'] . ' already exists in Applicant reference Information');
                }

                $applicantRef->applicant_id = $this->applicant->id;
            }

            $applicantRef->name = $data['name'];
            $applicantRef->organization = $data['organization'];
            $applicantRef->designation = $data['designation'];
            $applicantRef->mobile = $data['mobile'];
            $applicantRef->email = $data['email'] ?? null;
            $applicantRef->address = $data['address'] ?? null;
            $applicantRef->phone_office = $data['phone_office'] ?? null;
            $applicantRef->phone_home = $data['phone_home'] ?? null;
            $applicantRef->relation = $data['relation'] ?? null;
            $applicantRef->save();

            $this->applicant->updateProfileResumeStatus('reference');
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

        return $this->getReferenceInformation($applicantRef);
    }

    /**
     * Get applicant reference information
     *
     * @return boolean
     */
    public function saveCvData(array $data): bool
    {

        try {

            // save applicant data

            $this->applicant->email = $this->applicant->email ?? $data['email'] ?? null;
            $this->applicant->mobile = $this->applicant->mobile ?? $data['phone'] ?? null;
            $this->applicant->first_name = $this->applicant->first_name ?? $data['name'] ?? null;
            if (isset($data['personal details']['date']) && $data['personal details']['date']) {
                $this->applicant->dob = date('Y-m-d', strtotime($data['personal details']['date']));
            }
            if (isset($data['personal details']['gender']) && $data['personal details']['gender']) {
                $this->applicant->gender = strtolower($data['personal details']['gender']);
            }
            $this->applicant->is_cv_parsed = 1;

            $this->applicant->save();

            // save personal information

            $personal = $this->applicant->personalInformation()->first();
            if (empty($personal)) {
                $personal = new ApplicantInfo();
                $personal->applicant_id = $this->applicant->id;
            }

            if (isset($data['personal details']['father']) && $data['personal details']['father']) {
                $personal->father_name = $data['personal details']['father'];
            }
            if (isset($data['personal details']['mother']) && $data['personal details']['mother']) {
                $personal->mother_name = $data['personal details']['mother'];
            }
            if (isset($data['personal details']['marital']) && $data['personal details']['marital']) {
                $personal->marital_status = strtolower($data['personal details']['marital']);
            }
            if (isset($data['personal details']['nationality']) && $data['personal details']['nationality']) {
                $personal->nationality = $data['personal details']['nationality'];
            }
            if (isset($data['personal details']['religion']) && $data['personal details']['religion']) {
                $personal->religion = strtolower($data['personal details']['religion']);
            }
            if (isset($data['personal details']['permanent']) && $data['personal details']['permanent']) {
                $personal->permanent_address = $data['personal details']['permanent'];
            }
            if (isset($data['personal details']['current']) && $data['personal details']['current']) {
                $personal->present_address = $data['personal details']['current'];
            }
            if (isset($data['application information']['present salary']) && $data['application information']['present salary']) {
                $personal->present_salary = $data['application information']['present salary'];
            }
            if (isset($data['application information']['expected salary']) && $data['application information']['expected salary']) {
                $personal->expected_salary = $data['application information']['expected salary'];
            }
            if (isset($data['career objective']) && $data['career objective']) {
                $personal->career_objective = $data['career objective'];
            }
            if (isset($data['career summary']) && $data['career summary']) {
                $personal->career_summary = $data['career summary'];
            }
            if (isset($data['special qualification']) && $data['special qualification']) {
                $personal->special_qualification = $data['special qualification'];
            }

            $personal->save();

        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

        return true;
    }

}