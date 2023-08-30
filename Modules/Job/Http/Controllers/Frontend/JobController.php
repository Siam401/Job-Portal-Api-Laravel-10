<?php

namespace Modules\Job\Http\Controllers\Frontend;

use App\Http\Resources\PaginationResource;
use App\Traits\ResponseJSON;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Company\Services\JobCompanyService;
use Modules\Job\Http\Resources\JobDetailResource;
use Modules\Job\Models\Job;
use Modules\Job\Services\FrontendService;

class JobController extends Controller
{
    use ResponseJSON;

    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function activeJobs(Request $request)
    {
        $jobs = (new FrontendService)->getJobsWithPagination($request);

        return $this->success()
            ->message('Job details fetched successful')
            ->response(
                PaginationResource::collection($jobs)->response()->getData(true)
            );
    }

    /**
     * Show the form for creating a new resource.
     * @return JsonResponse
     */
    public function jobDetail(string $code)
    {
        $job = (new FrontendService)->jobDetail($code);

        if (empty($job)) {
            return $this->message('Job not found')->error(404);
        } elseif (intval($job->status) !== Job::STATUS_ACTIVE) {
            return $this->message('Job not available')->error();
        } elseif (Carbon::now()->isAfter($job->end_date)) {
            return $this->message('Job is expired')->error();
        }

        $job->company_summary = JobCompanyService::getSummary(null, $job->wing, $job->branch);

        return $this->success()
            ->message('Job details fetched successful')
            ->response(
                new JobDetailResource($job)
            );
    }
}