<?php

namespace Modules\Applicant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicantMeta extends Model
{

    protected $fillable = ['applicant_id', 'key_name', 'key_value'];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

}
