<?php

namespace Modules\User\Listeners;

use App\Services\Notification\SmsService;
use Exception;
use Modules\User\Events\UserForgetPassword;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\User\Models\User;

class SendResetPasswordSms
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
        $notifySms = new SmsService();
        if ($event->type == 'mobile') {

            $notifySms
                ->setMessage(
                    $this->message($event->user)
                )
                ->receivers(collect([$event->user->mobile]))
                ->send();


            if ($notifySms->error && isset($notifySms->error['message'])) {
                throw new Exception($notifySms->error['message']);
                // session()->flash('error', $notifySms->error['message']);
            }
        }
    }

    private function message(User $user)
    {
        return "Your verification code is {$user->v_code}";
    }
}