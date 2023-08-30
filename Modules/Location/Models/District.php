<?php

namespace Modules\Location\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    protected $fillable = [];


    public function division()
    {
        return $this->belongsTo(Division::class);
    }

}
