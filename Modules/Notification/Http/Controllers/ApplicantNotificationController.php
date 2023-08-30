<?php

namespace Modules\Notification\Http\Controllers;

use App\Traits\ResponseJSON;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Notification\Http\Resources\UserNotifications;
use Modules\Notification\Models\Notification;
use Modules\User\Models\User;

class ApplicantNotificationController extends Controller
{
    use ResponseJSON;

    /**
     * Get user notification list with pagination\
     *
     * @return Renderable
     */
    public function index(Request $request)
    {

        Notification::where('user_id', auth('sanctum')->user()->id)->where('status', 0)->update(['status' => Notification::STATUS_SEEN]);

        $notifications = $request->user()->notifications()->select(
            'id',
            'title',
            'subject',
            'status',
            'send_at',
        )->latest('id')->paginate($request->per_page ?? 10);

        return $this->success()->message('User notifications')->response(UserNotifications::collection($notifications)->response()->getData(true));

    }

    /**
     * Get notification detail
     *
     * @param Notification $notification
     * @return Renderable
     */
    public function getDetail(Notification $notification) {
        if($notification->user_id != auth('sanctum')->user()->id) {
            return $this->resultCode(1)->message('Notification not found')->response();
        }

        $details = Cache::remember('notification_details_' . $notification->id, 60, function() use ($notification) {
            return $notification->getContent();
        });

        if($notification->status !== Notification::STATUS_READ) {
            $notification->update(['status' => Notification::STATUS_READ]);
        }

        return $this->success()->message('User notification fetched successfully')->response([
            'id' => $notification->id,
            'date' => Carbon::parse($notification->send_at)->format('j M, Y'),
            'send_at' => $notification->send_at->toIso8601String(),
            'title' => $notification->title,
            'subject' => $notification->subject,
            'content' => $details->content,
        ]);
    }

    /**
     * Soft delete user notification
     *
     * @param Notification $notification
     * @return Renderable
     */
    public function removeNotification(Notification $notification) {
        if($notification->user_id != auth('sanctum')->user()->id) {
            return $this->resultCode(1)->message('Notification not found')->response();
        }

        $notification->delete();

        return $this->success()->message('Notification removed successfully')->response();
    }

}
