<?php

namespace Modules\User\Traits;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

trait VerifyEmails {
/**
     * Send a new email verification notification.
     */
    public function sendVerification(Request $request): Renderable
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->success()->message('Email already verified')->response();
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->success()->message('Email verification link is sent')->response();
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function processVerification(EmailVerificationRequest $request): Renderable
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->success()->message('Email already verified')->response();
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->success()->message('Email verified successfully')->response();
    }
}