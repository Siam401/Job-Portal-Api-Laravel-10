<?php

namespace Modules\Notification\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserNotifications extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'title' => $this->title,
            'status' => $this->status,
            'send_at' => $this->send_at->toIso8601String()
        ];
    }
}
