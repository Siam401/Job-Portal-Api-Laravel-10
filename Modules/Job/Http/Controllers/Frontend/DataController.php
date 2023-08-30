<?php

namespace Modules\Job\Http\Controllers\Frontend;

use App\Traits\ResponseJSON;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Job\Services\FrontendService;

class DataController extends Controller
{
    use ResponseJSON;
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function jobCategories()
    {

        return $this->success()
            ->message('Job category fetched successful')
            ->response(
                (new FrontendService())->categoryOptions()
            );
    }

}