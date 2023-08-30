<?php

namespace Modules\User\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\User\Http\Resources\UserInformation;
use Modules\User\Models\User;
use Modules\User\Models\UserSocial;
use Modules\User\Services\UserService;

trait SocialLogin
{
    /**
     * Process User Social Login Request
     *
     * @return Responsable
     */
    public function socialLogin(Request $request)
    {
        $user = $this->checkExistingAccount($request);

        if (empty($user)) {
            // $request = $this->prepareRequest($request);
            $user = $this->socialAccountRegistration($request);

            if (!$user) {
                throw new HttpResponseException(
                    $this->success()
                        ->message('Need user information for registration')
                        ->response([
                            'social_id' => $request->social_id,
                            'type' => $request->type,
                            'need_registration' => true,
                        ])
                );
            }
        } else {
            $userSocial = UserSocial::where('social_id', $request->social_id)
                ->where('type', $request->type)
                ->first(); 
            if (empty($userSocial)) { (new UserService($user))->addSocialAccount($request);
            }
        }

        return $this->success()
            ->message('Login successful', )
            ->response([
                'token' => $user->createToken('authToken')->plainTextToken,
                'user_type' => $user->user_type,
                'user' => new UserInformation($user),
            ]);
    }

    /**
     * Check if user already has an account
     *
     * @param Request $request
     * @return User|bool
     */
    private function checkExistingAccount(Request $request)
    {
        $userSocial = UserSocial::where('social_id', $request->social_id)
            ->where('type', $request->type)
            ->first();

        $user = User::where('email', $request->email)
            ->first();

        if (!empty($userSocial)) {
            return $userSocial->user;
        }

        if (!empty($user)) {
            return $user;
        }

        return [];
    }

    /**
     * Prepare request to work like registration
     *
     * @param Request $request
     * @return Request
     */
    private function prepareRequest(Request $request): Request
    {

        if ($request->has('photo_url')) {
            $url = $request->get('photo_url');

            $contents = file_get_contents($url);
            $name = 'download' . rand(100, 999) . '.png';

            if (Storage::put($name, $contents)) {
                $request->merge(['photo' => new UploadedFile(Storage::path($name), $name)]);
                // $request->files->set('photo', new UploadedFile(Storage::path($name), $name));
                // dd(
                //     $request->all()
                // );

                dd($request->file('photo'));
                $upload = uploadFile($request->file('photo'), 'photo');
                
                if ($upload) {
                    unlink(Storage::path($name));
                }
            }
            dd(1234);
        }
        return empty($request) ? false : $request;
    }

    private function socialAccountRegistration(Request $request): User
    {
        $user = (new UserService())->createUser($request, 'applicant', true);

        // Set user email as verified
        $user->update([
            'status' => 1,
            'email_verified_at' => now()
        ]);

        return $user;
    }

}