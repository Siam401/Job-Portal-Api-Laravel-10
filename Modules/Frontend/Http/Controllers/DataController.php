<?php

namespace Modules\Frontend\Http\Controllers;

use App\Traits\ResponseJSON;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Frontend\Models\Education;
use Modules\Frontend\Models\SpecialSkill;
use Modules\Job\Models\JobFunction;

class DataController extends Controller
{
    use ResponseJSON;

    /**
     * Get array of job functions
     *
     * @return JsonResponse
     */
    public function getJobFunctions()
    {
        $data = Cache::remember('job_functions', 60 * 60 * 24, function () {
            return JobFunction::select('id as value', 'name as text')->where('is_active', 1)->get()->toArray();
        });

        return $this->success()
            ->message('Job functions fetched successful')
            ->response(
                $data
            );
    }

    /**
     * Get array of special skills
     *
     * @return JsonResponse
     */
    public function getSpecialSkills()
    {
        $data = Cache::remember('special_skills', 60 * 60 * 24, function () {
            return SpecialSkill::select('id as value', 'name_bangla as text')->where('is_active', 1)->get()->toArray();
        });

        return $this->success()
            ->message('Special skills fetched successful')
            ->response(
                $data
            );
    }

    /**
     * Get array of educations
     *
     * @return JsonResponse
     */
    public function getEducations()
    {
        $data = Cache::remember('educations', 60 * 60 * 24, function () {
            return Education::select('id as value', 'name as text')->where('is_active', 1)->orderBy('serial')->get()->toArray();
        });

        return $this->success()
            ->message('Education data-list fetched successful')
            ->response(
                $data
            );
    }

}