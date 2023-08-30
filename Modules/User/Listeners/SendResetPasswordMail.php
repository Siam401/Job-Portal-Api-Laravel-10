<?php

namespace Modules\User\Listeners;

use Modules\User\Events\UserForgetPassword;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\User\Notifications\SendOtpMail;

class SendResetPasswordMail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param UserForgetPassword $event
     * @return void
     */
    public function handle(UserForgetPassword $event)
    {
        $user = $event->user;
        if($event->type == 'email') {
            Notification::send($user, new SendOtpMail($user->v_code ?? ''));
        }
    }
}
