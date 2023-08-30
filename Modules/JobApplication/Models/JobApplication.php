<?php

namespace Modules\JobApplication\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\JobInterview\Models\JobInterview;

class JobApplication extends Model
{
    const STAGE_APPLIED = 1;
    const STAGE_SHORTLIST = 2;
    const STAGE_INTERVIEW = 3;
    const STAGE_REJECTED = 4;
    const STAGE_WAITING = 5;
    const STAGE_HIRED = 6;

    protected $fillable = [
        'applicant_id',
        'photo',
        'cover_letter',
        'job_id',
        'stage',
        'sequence',
        'is_seen',
    ];

    /**
     * Get array of job codes where applicant applied
     *
     * @param integer $applicantId
     * @return array
     */
    public static function getAppliedJobCodes(int $applicantId): array
    {
        return self::select('jobs.code')->leftJoin('jobs', 'jobs.id', 'job_applications.job_id')->where('applicant_id', $applicantId)
            ->pluck('code')
            ->toArray();
    }

    public function scopeIsSeen($query)
    {
        return $query->where('is_seen', true);
    }

    public function questionAnswers()
    {
        return $this->hasMany(JobApplicationQa::class);

    }

    public function interviews()
    {
        return $this->hasMany(JobInterview::class);

    }
}