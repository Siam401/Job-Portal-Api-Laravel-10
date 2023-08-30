<?php

namespace Modules\Job\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'job_type',
        'workplace',
        'salary',
        'gender',
        'form_visibility',
        'questions',
        'skills',
        'age_min',
        'age_max',
        'description',
        'responsibility',
        'education',
        'benefit',
        'additional',
        'experience',
        'is_exp_required',
        'min_exp',
        'max_exp'
    ];

    /**
     * Get form visibility array
     */
    protected function formVisibility(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => !empty($value) ? explode(',', $value) : [],
        );
    }

    /**
     * Get form questions array
     */
    protected function questions(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => !empty($value) ? explode(',', $value) : [],
        );
    }
}