<?php

namespace Modules\JobApplication\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class ApplicationStage extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected static function getStages()
    {
        return Cache::rememberForever('application_stages', function () {
            return self::select('id', 'name', 'slug')->get();
        });
    }
}
