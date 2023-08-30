<?php

namespace Modules\Job\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class JobFunction extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Get all job categories
     *
     * @return array
     */
    public static function getCategories(): Collection
    {
        return Cache::remember('job_categories', 3600, function () {
            return JobFunction::select('name', 'id', 'is_active')->get();
        });
    }

    public static function getGluedFunctions($data) {
        if(empty($data)) return '';

        $gluedFunctions = self::select('name')->whereIn('id', $data)->get()->pluck('name')->toArray();
        return implode(', ', $gluedFunctions);
    }
}