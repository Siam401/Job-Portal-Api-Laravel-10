<?php

namespace Modules\User\Http\Controllers;

use App\Services\FileUpload\FileUpload;
use App\Traits\ResponseJSON;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Notification\Http\Resources\UserNotifications;
use Modules\Notification\Models\Notification;
use Modules\User\Http\Resources\UserInformation;
use Modules\User\Models\Applicant;
use Modules\User\Models\User;

class UserProfileController extends Controller
{
    use ResponseJSON;

    /**
     * Get user information with notifications
     *
     * @param Request $request
     * @return Renderable
     */
    public function getInformation(Request $request)
    {
        $user = $request->user();
        return $this->success()
            ->message('Get user information')
            ->response([
                'user' => new UserInformation($user),
                'notifications' => [
                    'unseen' => $user->notifications()->unseenCount(),
                    'recent' => UserNotifications::collection(
                        Notification::getRecentNotifications($user->id)
                    ),
                ],
            ]);
    }

    /**
     * Change user password
     *
     * @param Request $request
     * @return Renderable
     */
    public function changePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            // Handle validation error
            return $this->message($validator->errors()->first())->resultCode(10)->response();
        }

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return $this->resultCode(1)->message('Old password is incorrect')->response();
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return $this->success()->message('Password changed successfully')->response();

    }

    /**
     * Update user photo
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function updatePhoto(Request $request)
    {
        $validator = validator($request->all(), [
            'photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ], [
            'photo' => 'Photo must be a file of type: jpg, jpeg, png & max size 2MB'
        ]);

        if ($validator->fails()) {
            return $this->resultCode(1)->message($validator->errors()->first())->response();
        }

        DB::beginTransaction();
        try {
            $user = $request->user();
            $user->photo = uploadFile($request->file('photo'), 'photo');
            $user->save();

            if ($user->user_type == 'applicant') {
                $applicant = $user->applicant;
                $applicant->photo = $user->photo;
                $applicant->save();

                $applicant->updateProfileResumeStatus('photo');
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->resultCode(1)->message($th->getMessage())->response();
        }

        return $this->success()->message('Photo updated successfully')->response([
            'photo' => FileUpload::getUrl($user->photo)
        ]);
    }

    /**
     * Delete user photo
     *
     * @param Request $request
     * @return JsonResource
     */
    public function deletePhoto(Request $request)
    {
        $user = $request->user();

        DB::beginTransaction();
        try {
            $photoFile = $user->photo;
            $user->photo = null;
            $user->save();

            if ($user->user_type == 'applicant') {
                $applicant = $user->applicant;
                $applicant->photo = null;
                $applicant->save();

                $applicant->degradeProfileResumeStatus('photo', 0);
            }

            FileUpload::remove($photoFile);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->message($th->getMessage())->error(500);
        }

        return $this->success()->message('Photo deleted successfully')->response();
    }
}