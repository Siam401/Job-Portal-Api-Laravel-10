<?php

namespace Modules\Notification\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationContent extends Model
{

    protected $fillable = [
        'notification_id',
        'msg_type',
        'receiver',
        'content',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
