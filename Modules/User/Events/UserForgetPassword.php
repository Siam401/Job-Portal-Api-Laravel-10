<?php

namespace Modules\User\Events;

use Illuminate\Queue\SerializesModels;
use Modules\User\Models\User;

class UserForgetPassword
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public User $user, public string $type = 'email')
    {
        //
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
