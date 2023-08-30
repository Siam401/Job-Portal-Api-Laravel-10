<?php

namespace Modules\Location\Http\Controllers;

use App\Traits\ResponseJSON;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Location\Models\Area;
use Modules\Location\Models\Country;
use Modules\Location\Models\District;
use Modules\Location\Models\Division;

class DataController extends Controller
{
    use ResponseJSON;

    /**
     * Get country list
     *
     * @return JsonResponse
     */
    public function countryList(string $option = null)
    {
        if (!empty($option)) {
            $countries = Country::select('name as text', 'id as value')->orderBy('name', 'ASC')->get();
        } else {
            $countries = Country::select('name', 'country_code', 'iso_code')->orderBy('name', 'ASC')->get();
        }

        if ($countries->count() <= 0) {
            return $this->message('No country found')->response([], 404);
        } else {
            return $this->success()
                ->message('Country list fetched successful')
                ->response($countries->toArray());
        }
    }

    /**
     * Get division list
     *
     * @return JsonResponse
     */
    public function divisionList(Request $request)
    {
        $divisions = Division::select('name as text', 'id as value');

        if($request->country_id) {
            $divisions = $divisions->where('country_id', $request->country_id);
        }

        return $this->success()
            ->message('Division list fetched successful')
            ->response($divisions->orderBy('name')->get());
    }

    public function districtList(Request $request)
    {
        if (empty($request->division_id)) {
            return $this->message('Division id is required')->error(422);
        }

        $districts = District::where('division_id', $request->division_id)->select('name as text', 'id as value')->orderBy('name')->get();

        return $this->success()
            ->message('District list fetched successful')
            ->response($districts);
    }

    public function areaList(Request $request)
    {
        if (empty($request->district_id)) {
            return $this->message('District id is required')->error(422);
        }

        $districts = Area::where('district_id', $request->district_id)->select('name as text', 'id as value')->orderBy('name')->get();

        return $this->success()
            ->message('Area list fetched successful')
            ->response($districts);
    }

    public function getTimezones()
    {
        $timezones = config('dataset.timezones.all', []);
        $data = [];

        foreach ($timezones as $key => $timezone) {
            $data[] = [
                'text' => $timezone,
                'value' => $key
            ];
        }

        return $this->success()
            ->message('Timezone list fetched successful')
            ->response(
                $data
            );
    }
}