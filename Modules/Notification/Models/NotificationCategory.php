<?php

namespace Modules\Notification\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationCategory extends Model
{

    protected $fillable = ['name', 'description'];

    public function templates()
    {
        return $this->hasMany(NotificationTemplate::class);
    }

}
