<?php

namespace Modules\Applicant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicantEducation extends Model
{
    use HasFactory;

    protected $table = 'applicant_educations';

    protected $fillable = [];

    protected $casts = [
        'education_id' => 'integer',
        'duration' => 'float',
    ];
}
