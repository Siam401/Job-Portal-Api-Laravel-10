<?php

namespace Modules\Job\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobQuestion extends Model
{

    protected $fillable = [];

    protected function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
