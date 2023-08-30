<?php

namespace Modules\JobApplication\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobApplicationLog extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\JobApplication\Database\factories\JobApplicationLogFactory::new();
    }
}
