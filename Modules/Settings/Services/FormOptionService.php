<?php

namespace Modules\Settings\Services;

use App\Traits\ResponseJSON;
use Illuminate\Support\Facades\Cache;
use Modules\Frontend\Models\SocialLink;
use Modules\Settings\Models\FormOption;
use Modules\Settings\Models\Setting;

class FormOptionService
{
    public static function getOptions(string $name)
    {
        return Cache::rememberForever('options_' . $name, function () use ($name) {
            return FormOption::select('option_title', 'option_value')->where('name', $name)->get()->pluck('option_title', 'option_value')->toArray();
        });

    }

    public static function getOptionTitle(string $name, $value)
    {

        return self::getOptions($name)[$value] ?? null;

    }

    public static function getOptionArray(string $name): array
    {
        $itemArray = self::getOptions($name);

        return $itemArray ? array_keys($itemArray) : [];

    }

}