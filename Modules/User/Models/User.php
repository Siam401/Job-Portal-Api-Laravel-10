<?php

namespace Modules\User\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Notification\Models\Notification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'user_type',
        'name',
        'reg_type',
        'email',
        'country_code',
        'mobile',
        'password',
        'photo',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = ['v_code_send_at' => 'datetime'];

    /**
     * OTP resend time in minutes
     *
     * @return integer
     */
    public static function otpResendTime():int
    {
        return config('app.test_mode') ? 1 : config('app.time_otp_resend');
    }

    /**
     * OTP lifetime in minutes
     *
     * @return integer
     */
    public static function otpLifetime():int
    {
        return config('app.time_otp_lifetime');
    }

    /**
     * User relationship with applicant
     *
     * @return HasOne
     */
    public function applicant()
    {
        return $this->hasOne(Applicant::class);
    }

    /**
     * User relationship with user Social Accounts
     *
     * @return HasMany
     */
    public function userSocials()
    {
        return $this->hasMany(UserSocial::class);
    }

    /**
     * User relationship with notification
     *
     * @return HasMany
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Add demo notifications to User for test mode
     *
     * @return bool
     */
    public function demoNotification()
    {
        if (Notification::where('user_id', $this->id)->exists() === false && config('app.test_mode')) {
            for ($i = 5; $i > 0; $i--) {
                $notification = Notification::create([
                    'user_id' => $this->id,
                    'category' => 'job',
                    'title' => 'Job Confirmation',
                    'subject' => 'Congrats! You have a job confirmation mail',
                    'status' => Notification::STATUS_UNSEEN,
                    'send_at' => $i > 0 ? now()->subMinutes($i * 5 * 20) : now(),
                ]);

                $notification->contents()->create([
                    'msg_type' => 'email',
                    'receiver' => $this->email,
                    'content' => "Dear Xman,\nThank you for submitting your CV for the position of 'Software Engineer' and Congratulations for being shortlisted for the interview. Therefore, we would like to invite you for a written test at our office on Sunday, during below mentioned time range - Interview Details:\nDate range: 30/02/2023 - 31/02/2023\nTime range: 9:00 AM - 5:00 PM\nApproximate Length: 30 - 60 minutes\n\nLocation:\nNext It Limited\n1/B(3rd floor), Block-I, Road #8,\nBanani, Dhaka-1213.\nOffice Phone no:01931162113\n\nWe request you to confirm us your availability at the earliest through email or phone call. If you want to reach out to me before the interview, then please contact me at [Uzzal Hossain - 0188-1016626] Looking forward to meeting you.\n\nRegards,\nUzzal Hossain\nHR Executive, Next IT LTD",
                ]);
            }
        }
        return true;
    }
}