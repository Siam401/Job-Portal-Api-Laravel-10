<?php

namespace Modules\Settings\Http\Controllers;

use App\Traits\ResponseJSON;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Location\Models\Country;
use Modules\Settings\Models\FormOption;
use Modules\Settings\Models\Setting;

class DataController extends Controller
{
    use ResponseJSON;

    /**
     * Get application configurations
     *
     * @return JsonResponse
     */
    public function getConfigurations(): JsonResponse
    {
        $configs = [
            "isGroupCompany" => Setting::isGroupCompany() ? 1 : 0,
            "isNgo" => Setting::isNgoCompany() ? 1 : 0,
            "hasBranch" => Setting::isBranchEnabled() ? 1 : 0,
            "maintenanceMode" => Setting::getOption('maintenance_mode', 0) ? 1 : 0,
            "maintenanceText" => Setting::getOption('maintenance_description', 'Site under maintenance'),
            "socialAuth" => [
                'GOOGLE_CLIENT_ID' => Setting::getOption('social_google_client_id', ''),
                'GOOGLE_CLIENT_SECRET' => Setting::getOption('social_google_client_secret', ''),
                'FACEBOOK_CLIENT_ID' => Setting::getOption('social_facebook_client_id', ''),
                'FACEBOOK_CLIENT_SECRET' => Setting::getOption('social_facebook_client_secret', ''),
                'LINKEDIN_CLIENT_ID' => Setting::getOption('social_linkedin_client_id', ''),
                'LINKEDIN_CLIENT_SECRET' => Setting::getOption('social_linkedin_client_secret', ''),
            ],
        ];

        return $this->success()
            ->message('Form options fetched successful')
            ->response(
                $configs
            );
    }

    /**
     * Get form option data for select/radio/checkbox fields
     *
     * @return Renderable
     */
    public function getFormOptions()
    {
        $formOptions = Cache::rememberForever('form_options', function () {
            return [
                'gender' => FormOption::getData('gender', 'string'),
                'workplace' => FormOption::getData('workplace', 'string'),
                'job_status' => FormOption::getData('job_status', 'integer'),
                'job_type' => FormOption::getData('job_type', 'string'),
                'job_level' => FormOption::getData('job_level', 'string'),
                'marriage' => FormOption::getData('marriage', 'string'),
                'religion' => FormOption::getData('religion', 'string'),
                'nationality' => Country::nationalityOptions(),
            ];
        });

        return $this->success()
            ->message('Form options fetched successful')
            ->response(
                $formOptions
            );
    }
}