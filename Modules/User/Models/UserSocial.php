<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserSocial extends Model
{

    protected $fillable = [
        'user_id',
        'type',
        'social_id',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
