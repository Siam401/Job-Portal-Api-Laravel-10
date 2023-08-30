<?php

namespace Modules\JobApplication\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobApplicationQa extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_application_id',
        'question',
        'answer'
    ];

}