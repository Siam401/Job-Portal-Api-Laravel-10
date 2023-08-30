<?php

namespace Modules\User\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\User\Events\UserForgetPassword;
use Modules\User\Listeners\SendResetPasswordMail;
use Modules\User\Listeners\SendResetPasswordSms;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserForgetPassword::class => [
            SendResetPasswordMail::class,
            SendResetPasswordSms::class,
        ],
    ];
}