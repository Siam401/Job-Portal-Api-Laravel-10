<?php

namespace Modules\User\Traits;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\User\Http\Requests\UserRegistrationRequest;
use Modules\User\Models\User;
use Modules\User\Services\UserService;

trait RegisterUsers
{

    public function register(UserRegistrationRequest $request)
    {

        $user = (new UserService())->createUser($request, 'applicant');

        if (config('app.test_mode') || config('app.env') == 'local') {
            // Execute this code only in test mode
            $user->status = 1;
            $user->email_verified_at = now();
            $user->save();

            $user->demoNotification();
        } else {
            event(new Registered($user));
        }

        // $token = $user->createToken('authToken')->accessToken;

        return $this->success()
            ->message('Registration successful! Please check your email for verification.')
            ->response();
    }

}