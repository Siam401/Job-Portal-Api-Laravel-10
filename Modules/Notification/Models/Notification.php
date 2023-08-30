<?php

namespace Modules\Notification\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    /**
     * Notification Status Definitions
     */
    const STATUS_UNSEEN = 0;
    const STATUS_SEEN = 1;
    const STATUS_READ = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'category',
        'model_id',
        'title',
        'subject',
        'status',
        'send_at',
    ];

    /**
     * Protected attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];


    /**
     * Protected attributes that are casts.
     *
     * @var array
     */
    protected $casts = [
        'send_at' => 'datetime',
    ];


    /**
     * Get recent user notifications.
     *
     * @param integer $userId
     * @return Collection
     */
    public static function getRecentNotifications(int $userId)
    {
        return self::select('id', 'subject', 'status', 'send_at', 'title')->where('user_id', $userId)->latest()->limit(4)->get();
    }

    /**
     * Get the content for the notification.
     *
     * @return NotificationContent
     */
    public function getContent()
    {
        $content = $this->emailContent()->first();
        if (empty($content)) {
            $content = $this->smsContent()->first() ?? $this->contents()->first();
        }
        return $content;
    }

    /**
     * Apply Eloquent scope to get unseen notifications count.
     */
    public function scopeUnseenCount($query)
    {
        return $query->where('status', self::STATUS_UNSEEN)->count();
    }

    /**
     * Apply Eloquent relationship for notification contents.
     *
     * @return HasMany
     */
    public function contents()
    {
        return $this->hasMany(NotificationContent::class);
    }

    /**
     * Apply Eloquent relationship for email notification content
     *
     * @return HasOne
     */
    public function emailContent()
    {
        return $this->hasOne(NotificationContent::class)->where('msg_type', 'email');
    }

    /**
     * Apply Eloquent relationship for sms notification content
     *
     * @return HasOne
     */
    public function smsContent()
    {
        return $this->hasOne(NotificationContent::class)->where('msg_type', 'sms');
    }

}