<?php

namespace Modules\Frontend\Http\Controllers;

use App\Traits\ResponseJSON;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Frontend\Http\Resources\Client\HomeResource;
use Modules\Frontend\Http\Resources\Client\HomeWingCityResource;
use Modules\Frontend\Http\Resources\Client\LayoutResource;
use Modules\Frontend\Models\Section;
use Modules\Job\Services\FrontendService;
use Modules\Settings\Services\ThemeService;

class PageController extends Controller
{
    use ResponseJSON;

    /**
     * Get home page information
     *
     * @return JsonResponse
     */
    public function getHome()
    {
        $data = Section::query()->where('slug', '<>', 'how-to-apply')->with('sectionItems', function ($query) {
            $query->orderBy('serial', 'asc');
        })->get();

        if (!$data || $data->count() == 0) {
            return $this->message('Home page information not found')->error(404);
        }

        $sections = [];
        foreach ($data as $value) {
            $slug = str_replace('-', '_', $value->slug);

            $frontendService = new FrontendService;
            if ($value->slug === 'job-wings') {
                $value['items'] = $frontendService->jobCountByWings();
                $sections[$slug] = new HomeWingCityResource($value);
            } elseif ($value->slug === 'job-cities') {
                $value['items'] = $frontendService->jobCountByCities();
                $sections[$slug] = new HomeWingCityResource($value);
            } else {
                $sections[$slug] = new HomeResource($value);
            }
        }

        return $this->success()
            ->message('Home page information fetched successful')
            ->response($sections);
    }

    /**
     * Get home section information
     *
     * @param string $name
     * @return JsonResponse
     */
    public function getSections(string $name = 'banner')
    {
        $homeSection = Section::where('slug', $name)->with('sectionItems', function ($query) {
            $query->orderBy('serial', 'asc');
        })->first();

        if (!$homeSection) {
            return $this->message('Home Section not found')->error(404);
        }

        return $this->success()
            ->message('Home Section fetched successful')
            ->response(new HomeResource($homeSection));

    }

    /**
     * Get client theme layout information
     *
     * @return JsonResponse
     */
    public function getLayouts()
    {
        $themeServie = new ThemeService;

        $layoutData = $themeServie->getThemeData();

        return $this->success()
            ->message('Layout data fetched successful')
            ->response(new LayoutResource($layoutData));
    }

    /**
     * Get number of jobs by wings
     *
     * @return JsonResponse
     */
    public function getJobWings()
    {
        $wingSection = Section::where('slug', 'job-wings')->first();

        if (!$wingSection) {
            return $this->message('Wing not found')->error(404);
        }

        $frontendService = new FrontendService;
        $winsSectionItem = $frontendService->jobCountByWings();

        $wingSection["items"] = $winsSectionItem;

        return $this->success()
            ->message('Job Wing Section fetched successful')
            ->response(new HomeWingCityResource($wingSection));
    }

    /**
     * Get number of jobs by cities
     *
     * @return JsonResponse
     */
    public function getJobCities()
    {
        $citySection = Section::where('slug', 'job-cities')->first();

        if (!$citySection) {
            return $this->message('Home Section not found')->error(404);
        }

        $frontendService = new FrontendService;
        $citySectionItem = $frontendService->jobCountByCities();

        $citySection["items"] = $citySectionItem;

        return $this->success()
            ->message('Job Wing Section fetched successful')
            ->response(new HomeWingCityResource($citySection));
    }
}