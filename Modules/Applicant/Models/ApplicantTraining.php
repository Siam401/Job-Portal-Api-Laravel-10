<?php

namespace Modules\Applicant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicantTraining extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $casts = [
        'country_id' => 'integer',
    ];
}
