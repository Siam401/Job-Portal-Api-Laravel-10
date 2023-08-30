<?php

namespace Modules\Job\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{

    protected $fillable = ['name', 'language'];

    public static function getSkills(){

        return self::where('language', 0)->where('stop', 0)->pluck('name')->toArray();
    }

    public static function getLanguages(){

        return self::where('language', 1)->where('stop', 0)->pluck('name')->toArray();
    }

}