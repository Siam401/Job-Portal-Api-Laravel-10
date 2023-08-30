<?php

namespace Modules\Location\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Modules\Location\Models\Division;

class DivisionService
{
    public static function getDivisions()
    {
        return Cache::remember('divisions', 60 * 60 * 24, function () {
            return Division::select('id', 'country_id', 'name', 'slug')->get();
        });
    }

    public static function getDivisionIds(string $slugs)
    {
        return self::getDivisions()->whereIn('slug', explode(',', $slugs))->pluck('id')->toArray();
    }

}
