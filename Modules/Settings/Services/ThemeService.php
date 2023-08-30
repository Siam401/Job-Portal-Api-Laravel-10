<?php

namespace Modules\Settings\Services;

use App\Traits\ResponseJSON;
use Illuminate\Support\Facades\Cache;
use Modules\Frontend\Models\SocialLink;
use Modules\Settings\Models\Setting;

class ThemeService
{

    use ResponseJSON;

    public function getThemeData()
    {
        return [
            "header" => $this->siteInformation(),
            "logo" => $this->siteLogo(),
            "footer" => $this->footers(),
            "theme" => [],
            "seo" => $this->siteSeo(),
            "social_links" => $this->socialLinks()
        ];
    }

    public function siteLogo()
    {
        $logo = Setting::where('name', 'site_logo')->first()?->value;

        return empty($logo) ? "frontend/logo.png" : $logo;
    }

    public function siteSeo()
    {
        return Cache::remember('settings_seo', 3600, function () {
            return Setting::select('name', 'value')->where('name', 'LIKE', "seo_%")->pluck('value', 'name');
        });
    }

    public function siteInformation()
    {
        return Cache::remember('theme_information', 3600, function () {
            return Setting::select('name', 'value')->where('name', 'LIKE', "site_%")->pluck('value', 'name');
            ;
        });
    }

    public function footers()
    {
        return Cache::remember('theme_footers', 3600, function () {
            return Setting::select('name', 'value')->where('name', 'LIKE', "footer_%")->get()->pluck('value', 'name');
        });
    }

    public function socialLinks()
    {
        return Cache::remember('social_links', 3600, function () {
            return SocialLink::where('is_active', 1)->orderBy('serial', 'asc')->get();
        });
    }
}