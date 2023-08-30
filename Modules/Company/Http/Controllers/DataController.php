<?php

namespace Modules\Company\Http\Controllers;

use App\Traits\ResponseJSON;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Company\Models\Company;

class DataController extends Controller
{
    use ResponseJSON;

    /**
     * Display a listing of the Company wings
     * @return JsonResponse
     */
    public function getWings()
    {
        $wings = Company::select('name as text', 'code as value')->get()->toArray();

        return $this->success()
            ->message('Wings fetched successful')
            ->response($wings);
    }

}
