<?php

namespace Modules\User\Traits;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\User\Events\UserForgetPassword;
use Modules\User\Models\User;

trait ForgetResetPasswords
{

    /**
     * Send a reset code to the given user.
     *
     * @param  Request  $request
     * @return Responsable
     */
    public function forgetPassword(Request $request)
    {

        list($user, $requestType) = $this->getRequestedUser($request);

        $user->v_code = rand(100000, 999999);
        $user->v_code_send_at = now();
        $user->save();


        event(new UserForgetPassword($user, $requestType));

        return $this->success()->message(
            'Password reset code is sent to ' . $requestType
        )->response([
                    'type' => $requestType,
                    'username' => $request->username,
                ]);
    }

    /**
     * Get user from request.
     *
     * @param  Request  $request
     * @return array
     * @throws HttpResponseException
     */
    private function getRequestedUser(Request $request)
    {
        if (!$request->has('username')) {
            throw new HttpResponseException(
                $this->message('Please provide a valid email address or mobile number')->resultCode(10)->response()
            );
        }

        $requestType = false;

        if (filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $request->username)->first();
            $requestType = 'email';
        } else {
            $user = User::where('mobile', $request->username)->first();
            $requestType = 'mobile';
        }

        if (!$user) {
            throw new HttpResponseException(
                $this->message('User not found')->resultCode(404)->response()
            );
        }

        return [$user, $requestType];
    }

    /**
     * Resend OTP if request is valid.
     *
     * @param Request $request
     * @return Responsable
     */
    public function resendOtp(Request $request)
    {

        list($user, $requestType) = $this->getRequestedUser($request);

        if ($user->v_code_send_at && $user->v_code_send_at->diffInMinutes(now()) < User::otpResendTime()) {
            return $this->message('Please wait for ' . User::otpResendTime() . ' minutes to resend OTP')->resultCode(10)->response();
        }

        $user->v_code = rand(100000, 999999);
        $user->v_code_send_at = now();
        $user->save();

        event(new UserForgetPassword($user, $requestType));

        return $this->success()->message(
            'Password reset code is sent to ' . $requestType
        )->response([
                    'type' => $requestType,
                    'username' => $request->username,
                ]);
    }

    /**
     * Verify OTP if request is valid.
     *
     * @param Request $request
     * @return Responsable
     */
    public function verifyOtp(Request $request)
    {

        list($user, $requestType) = $this->getRequestedUser($request);
        
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|min:4|max:6',
        ]);

        if ($validator->fails()) {
            // Handle validation error
            return $this->message($validator->errors()->first())->resultCode(10)->response();
        } elseif ($user->v_code_send_at && $user->v_code_send_at->diffInMinutes(now()) > User::otpLifetime()) {
            return $this->message('OTP has been expired! Please resend')->resultCode(15)->response();
        } elseif ($user->v_code !== $request->otp) {
            return $this->message('OTP not matched')->resultCode(10)->response();
        }

        $user->status = 1;
        if ($requestType === "email") {
            $user->email_verified_at = now();
        } elseif ($requestType === "mobile") {
            $user->mobile_verified_at = now();
        }
        $user->save();

        return $this->success()->message(
            'User OTP is matched successfully'
        )->response([
                    'token' => base64_encode(json_encode(
                        [
                            'user_id' => $user->id,
                            'otp' => $request->otp,
                            'type' => 'forget_password'
                        ]
                    ))
                ]);
    }

    /**
     * Reset password if request is valid.
     *
     * @param Request $request
     * @return Responsable
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'password' => 'required|string|min:4|max:20',
            'confirm_password' => 'required|string|same:password',
        ]);

        if ($validator->fails()) {
            // Handle validation error
            return $this->message($validator->errors()->first())->resultCode(10)->response();
        }

        $userData = json_decode(base64_decode($request->token));

        $user = User::find($userData->user_id ?? 0);

        if (!$user) {
            return $this->message('User not found')->resultCode(404)->response();
        }

        $user->password = Hash::make($request->password);
        $user->v_code = $user->v_code_send_at = null;
        $user->save();

        return $this->success()->message(
            'User password is reset successfully'
        )->response();

    }

}