<?php

namespace Modules\Applicant\Services;

use Illuminate\Support\Facades\DB;
use Modules\Job\Models\Job;
use Modules\JobApplication\Models\JobApplication;
use Modules\JobInterview\Models\JobInterview;
use Modules\User\Models\Applicant;

class ApplicationReportService
{
    public static function getApplicationSubmissionCount(Applicant $applicant, $forLastOneMonth = false)
    {
        if ($forLastOneMonth) {
            return $applicant->applications()->where('created_at', '>=', now()->subMonth())->count();
        } else {
            return $applicant->applications()->count();
        }
    }

    public static function getResumeViewedCount(Applicant $applicant, $forLastOneMonth = false)
    {
        if ($forLastOneMonth) {
            return $applicant->applications()->isSeen()->where('created_at', '>=', now()->subMonth())->count();
        } else {
            return $applicant->applications()->isSeen()->count();
        }
    }

    public static function getInterviewScheduleCount(Applicant $applicant, $forLastOneMonth = false)
    {
        if ($forLastOneMonth) {
            return JobInterview::leftJoin('job_applications', 'job_applications.id', 'job_interviews.job_application_id')->where('applicant_id', $applicant->id)->where('interview_date', '>=', now()->subMonth())->count();
        } else {
            return JobInterview::leftJoin('job_applications', 'job_applications.id', 'job_interviews.job_application_id')->where('applicant_id', $applicant->id)->count();
        }
    }

    public static function getJobApplications(Applicant $applicant, $forLastOneMonth = false)
    {
        $applications = JobApplication::select(
            'jobs.title', 'wings.name as wing_name', 'branches.name as branch_name',
            DB::raw('(SELECT name as company_name FROM companies WHERE id = 1) as company_name'),
            'job_applications.is_seen', 'job_applications.created_at',
            DB::raw('IF(wings.name IS NULL, branches.address, wings.address) as location'),
            'job_applications.id', 'job_details.job_type', 'job_applications.stage',
        )
            ->join('jobs', 'jobs.id', 'job_applications.job_id')
            ->join('job_details', 'job_details.job_id', 'jobs.id')
            ->leftJoin('companies as wings', 'wings.id', 'jobs.wing_id')
            ->leftJoin('companies as branches', 'branches.id', 'jobs.branch_id')
            ->where('applicant_id', $applicant->id);

        if ($forLastOneMonth) {
            return $applications->where('job_applications.created_at', '>=', now()->subMonth())
                ->latest('job_applications.id')->paginate(10);
        } else {
            return $applications->latest('job_applications.id')->paginate(10);
        }
    }

}