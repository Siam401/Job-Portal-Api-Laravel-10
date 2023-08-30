<?php

namespace Modules\User\Models;

use App\Services\FileUpload\FileUpload;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Modules\Applicant\Models\ApplicantLanguage;
use Modules\Applicant\Models\ApplicantMeta;
use Modules\Applicant\Models\ApplicantCertification;
use Modules\Applicant\Models\ApplicantEducation;
use Modules\Applicant\Models\ApplicantExperience;
use Modules\Applicant\Models\ApplicantInfo;
use Modules\Applicant\Models\ApplicantReference;
use Modules\Applicant\Models\ApplicantSkill;
use Modules\Applicant\Models\ApplicantTraining;
use Modules\JobApplication\Models\JobApplication;
use Modules\JobInterview\Models\JobInterview;

class Applicant extends Model
{

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'country_code',
        'mobile',
        'gender',
        'dob',
        'resume',
        'photo'
    ];

    /**
     * Initiate profile completion status for a new applicant
     *
     * @param Applicant $applicant
     * @return boolean
     */
    public static function initProfileCompletionStatus(Applicant $applicant): bool
    {

        $applicantMeta = $applicant->metas()->where('key_name', 'like', 'profile_status_%')->get()->pluck('key_name');
        foreach (config('app.applicant.resume') as $key => $value) {
            $applicantMeta->contains('profile_status_' . $key) ?: $applicant->metas()->create([
                'key_name' => 'profile_status_' . $key,
                'key_value' => 0
            ]);
        }

        return true;

    }

    /**
     * Increase profile resume status upon adding Resume Information
     *
     * @param string $category
     * @return bool
     */
    public function updateProfileResumeStatus(string $category): bool
    {
        $configValue = config('app.applicant.resume.' . $category, null);

        if (!$configValue) {
            return false;
        }

        $applicantMeta = $this->metas()->where('key_name', 'profile_status_' . $category)->first();
        if (!$applicantMeta) {
            $applicantMeta = $this->metas()->create([
                'key_name' => 'profile_status_' . $category,
                'key_value' => $configValue
            ]);
        } elseif (intval($applicantMeta->key_value) <= 0) {
            $applicantMeta->update([
                'key_value' => $configValue
            ]);

            Cache::forget('resume_completion_status_' . $this->id);
        }

        return true;
    }

    /**
     * Decrease profile resume status upon Resume Information deletion
     *
     * @param string $category
     * @param integer $count
     * @return bool
     */
    public function degradeProfileResumeStatus(string $category, int $count): bool
    {
        $configValue = config('app.applicant.resume.' . $category, null);

        if ($count > 0) {
            return false;
        } elseif (!$configValue) {
            return false;
        }

        $this->metas()->where('key_name', 'profile_status_' . $category)->update([
            'key_value' => 0
        ]);

        Cache::forget('resume_completion_status_' . $this->id);

        return true;
    }

    /**
     * Get applicant resume download link if exists
     *
     * @param integer $userId
     * @param Applicant|null $applicant
     * @return string|null
     */
    public static function getResume(int $userId, Applicant $applicant = null)
    {
        if ($applicant) {
            return $applicant->resume ? FileUpload::getUrl($applicant->resume) : null;
        } else {
            $applicant = self::where('user_id', $userId)->first();

            return $applicant->resume ? FileUpload::getUrl($applicant->resume) : null;
        }
    }

    public function getInterviewSchedules()
    {
        $jobInterviews = JobApplication::select('jobs.title', 'job_interviews.id', 'job_interviews.interview_date', 'job_interviews.interview_time')
            ->join('job_interviews', 'job_interviews.job_application_id', '=', 'job_applications.id')
            ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->where('job_applications.applicant_id', $this->id)
            ->where('job_applications.stage', JobApplication::STAGE_INTERVIEW)
            ->where('job_interviews.status', JobInterview::STATUS_NOT_CALLED)
            ->orderByRaw("job_interviews.interview_date ASC, job_interviews.interview_time ASC")->get();

        $events = [];
        if ($jobInterviews->count() <= 0) {
            return $events;
        }

        foreach ($jobInterviews as $interview) {
            $interviewTime = Carbon::parse($interview->interview_date . ' ' . $interview->interview_time);
            $events[] = [
                'id' => $interview->id,
                'title' => 'Interview Schedule for The Position of ' . $interview->title,
                'date' => $interviewTime->format('j M'),
                'time' => $interviewTime->format('g:i A'),
                'start' => $interviewTime->toDateTimeString(),
                'end' => $interviewTime->addMinutes(60)->toDateTimeString(),
            ];
        }

        return $events;
    }

    /**
     * Get the user that owns the Applicant
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the meta values for the Applicant
     *
     * @return HasMany
     */
    public function metas()
    {
        return $this->hasMany(ApplicantMeta::class);
    }

    /**
     * Get all of the applications for the Applicant
     *
     * @return HasMany
     */
    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get the personalInformation associated with the Applicant
     *
     * @return HasOne
     */
    public function personalInformation()
    {
        return $this->hasOne(ApplicantInfo::class);
    }

    /**
     * Get the employment histories associated with the Applicant
     *
     * @return HasMany
     */
    public function experiences()
    {
        return $this->hasMany(ApplicantExperience::class);
    }

    /**
     * Get the personal information associated with the Applicant
     *
     * @return HasMany
     */
    public function educations()
    {
        return $this->hasMany(ApplicantEducation::class);
    }

    /**
     * Get professional certifications associated with the Applicant
     *
     * @return HasMany
     */
    public function certifications()
    {
        return $this->hasMany(ApplicantCertification::class);
    }

    /**
     * Get language skills associated with the Applicant
     *
     * @return HasMany
     */
    public function languages()
    {
        return $this->hasMany(ApplicantLanguage::class);
    }

    /**
     * Get trainings associated with the Applicant
     *
     * @return HasMany
     */
    public function trainings()
    {
        return $this->hasMany(ApplicantTraining::class);
    }

    /**
     * Get references associated with the Applicant
     *
     * @return HasMany
     */
    public function references()
    {
        return $this->hasMany(ApplicantReference::class);
    }

}