<?php

namespace Modules\JobInterview\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobInterview extends Model
{
    protected $fillable = [
        'hr_id',
        'job_application_id',
        'interview_date',
        'interview_time',
        'address',
        'message_email',
        'message_sms',
        'status',
    ];

    const STATUS_NOT_CALLED = 0;
    const STATUS_CALLED = 1;
    const STATUS_DONE = 2;
    const STATUS_CANCELLED = 3;

    public function application()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

}