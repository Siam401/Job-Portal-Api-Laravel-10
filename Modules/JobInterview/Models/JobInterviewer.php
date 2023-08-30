<?php

namespace Modules\JobInterview\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobInterviewer extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\JobInterview\Database\factories\JobInterviewerFactory::new();
    }
}
