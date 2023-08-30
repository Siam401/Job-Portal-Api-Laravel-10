<?php

namespace Modules\Applicant\Http\Controllers\Applicant;

use App\Http\Resources\PaginationResource;
use App\Traits\ResponseJSON;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Applicant\Services\ApplicantService;
use Modules\Applicant\Services\ApplicationReportService;
use Modules\JobApplication\Models\JobApplication;
use Modules\User\Models\Applicant;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    use ResponseJSON;

    /**
     * Get dashboard data for applicant
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $applicant = $request->user()->applicant;

        $responseData = [
            'resume_completion_percentage' => (new ApplicantService($applicant))->resumeCompletionPercentage(),
            'statistics' => [
                'all' => [
                    'applied_jobs' => ApplicationReportService::getApplicationSubmissionCount($applicant),
                    'resume_viewed' => ApplicationReportService::getResumeViewedCount($applicant),
                    'total_schedules' => ApplicationReportService::getInterviewScheduleCount($applicant),
                ],
                'month' => [
                    'applied_jobs' => ApplicationReportService::getApplicationSubmissionCount($applicant, true),
                    'resume_viewed' => ApplicationReportService::getResumeViewedCount($applicant, true),
                    'total_schedules' => ApplicationReportService::getInterviewScheduleCount($applicant, true),
                ]
            ],
            'events' => $this->getEvents($applicant)
        ];

        return $this->success()->message('Welcome to Applicant Dashboard')->response($responseData);
    }

    /**
     * Get applied job reports for applicant
     *
     * @param Request $request
     * @param string $type
     * @return JsonResponse
     */
    public function getJobApplications(Request $request, string $type = 'month')
    {
        $applicant = $request->user()->applicant;

        $applications = $type === 'month' ?
            ApplicationReportService::getJobApplications($applicant, true) :
            ApplicationReportService::getJobApplications($applicant);

        $applications->each(function ($application) {
            $application->apply_date = Carbon::parse($application->created_at)->format('j M, Y');
        });

        return $this->success()
            ->message('Applicant job applications fetched successfully')
            ->response(PaginationResource::collection($applications)->response()->getData(true));
    }

    /**
     * Get interview schedule events for applicant
     *
     * @param Applicant $applicant
     * @return array
     */
    private function getEvents(Applicant $applicant): array
    {
        $events = $applicant->getInterviewSchedules();

        if (empty($events) && config('app.test_mode')) {

            for ($i = 0; $i < 3; $i++) {
                $days = $i * $i + 1;
                $events[] = [
                    'id' => 100 + $i,
                    'title' => 'Interview Schedule for The Test Position',
                    'date' => now()->addDays($days)->format('j M'),
                    'time' => now()->addDays($days)->format('g:i A'),
                    'start' => now()->addDays($days)->toDateTimeString(),
                    'end' => now()->addDays($days)->addMinutes(60)->toDateTimeString(),
                ];
            }

        }

        return $events;
    }

    /**
     * Remove job application for applicant
     *
     * @param JobApplication $application
     * @param Request $request
     * @return JsonResponse
     */
    public function removeApplication(JobApplication $application, Request $request)
    {
        if ($application->applicant_id != $request->user()->applicant->id) {
            return $this->message('You are not authorized to perform this action')->error();
        } elseif ($application->stage !== JobApplication::STAGE_APPLIED) {
            return $this->message('You can not remove this application')->error();
        }

        $application->delete();

        return $this->success()->message('Application removed successfully')->response();
    }
}