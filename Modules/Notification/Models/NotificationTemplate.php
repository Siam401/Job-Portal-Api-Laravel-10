<?php

namespace Modules\Notification\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationTemplate extends Model
{

    const TYPE_EMAIL = 'email';
    const TYPE_SMS = 'sms';
    const TYPE_PUSH = 'push';

    protected $fillable = ['notification_category_id', 'type', 'subject', 'body'];

    public function category()
    {
        return $this->belongsTo(NotificationCategory::class, 'notification_category_id');
    }

}
