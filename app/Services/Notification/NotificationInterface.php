<?php

namespace App\Services\Notification;

use Illuminate\Support\Collection;

interface NotificationInterface
{
    public function receivers(Collection $users = null);
    public function send(): bool;
}
