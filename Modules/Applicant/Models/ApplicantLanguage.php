<?php

namespace Modules\Applicant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicantLanguage extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Applicant\Database\factories\ApplicantLanguageFactory::new();
    }
}
