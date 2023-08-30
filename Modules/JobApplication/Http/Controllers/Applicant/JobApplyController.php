<?php

namespace Modules\JobApplication\Http\Controllers\Applicant;

use App\Traits\ResponseJSON;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Job\Models\Job;
use Modules\JobApplication\Http\Requests\JobApplyRequest;
use Modules\JobApplication\Services\JobApplyService;

class JobApplyController extends Controller
{
    use ResponseJSON;

    /**
     * Process job application
     *
     * @param JobApplyRequest $request
     * @param string $jobCode
     * @return JsonResponse
     */
    public function __invoke(JobApplyRequest $request)
    {
        $job = Job::where('code', $request->job_code)->active()->first();
        if (empty($job)) {

            return $this->message('Job not found')->error(404);
        } elseif (Carbon::now()->isAfter($job->end_date)) {
            return $this->message('Job is expired')->error();
        }

        if ($request->has('mobile')) {
            $request->merge([
                'mobile' => formatBdMobileNumber($request->mobile)
            ]);
        }

        $jobApplyService = (new JobApplyService($request, $job))->handle();

        if ($jobApplyService->error) {
            return $this->resultCode(1)
                ->message($jobApplyService->error)
                ->response([], 500);
        }

        return $this->success()
            ->message('Job application submitted successful')
            ->response(
                [
                    'text' => 'Congratulations! Your application has been submitted successfully.',
                ]
            );
    }

}