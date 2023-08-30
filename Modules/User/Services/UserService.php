<?php

namespace Modules\User\Services;

use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\Applicant;
use Modules\User\Models\User;
use Illuminate\Support\Str;
use Modules\Applicant\Services\ApplicantService;

class UserService
{
    public User $user;

    function __construct(User $user = null)
    {
        $this->user = $user ?? new User();
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param string $userType
     * @return User $user
     * @throws HttpResponseException
     */
    public function createUser(Request $request, string $userType, bool $forSocialAccount = false): User
    {

        DB::beginTransaction();

        try {

            $this->user->user_type = $userType;
            $this->user->name = trim($request->first_name . ' ' . ($request->last_name ?? ''));
            $this->user->reg_type = $request->type ?? 'email';
            $this->user->email = $request->email;
            $this->user->country_code = '880'; //$request->country_code,
            $this->user->mobile = $request->mobile ? formatBdMobileNumber($request->mobile) : null;
            $this->user->password = Hash::make($request->password ?? Str::random(8));
            $this->user->photo = $request->hasFile('photo') ? uploadFile($request->file('photo'), 'photo') : null;
            $this->user->status = 0;

            $this->user->save();

            if ($forSocialAccount) {
                $this->addSocialAccount($request);
            }

            if ($userType === 'applicant') {
                $applicant = Applicant::create([
                    'user_id' => $this->user->id,
                    'first_name' => trim($request->first_name),
                    'last_name' => $request->last_name ?? null,
                    'email' => $request->email,
                    'country_code' => '880', //$request->country_code,
                    'mobile' => $this->user->mobile,
                    'gender' => $request->gender ?? 'male',
                    'photo' => $this->user->photo,
                    'resume' => $request->hasFile('resume') ? uploadFile($request->file('resume'), 'resume') : null
                ]);

                Applicant::initProfileCompletionStatus($applicant);

                // Parse applicant resume and save to resume profile tables if possible
                (new ApplicantService($applicant))->parseResumeIfNeeded();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'result_code' => 10,
                    'message' => $e->getMessage()
                ], 500)
            );
        }

        return $this->user;
    }

    /**
     * Add social account for applicant user
     *
     * @param Request $request
     * @return bool
     * @throws HttpResponseException
     */
    public function addSocialAccount(Request $request)
    {
        try {
            $meta = $request->meta ?? [];
            $this->user->userSocials()->create([
                'social_id' => $request->social_id,
                'type' => $request->type,
                'meta' => is_array($meta) ? json_encode($meta) : $meta
            ]);
        } catch (\Throwable $th) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'result_code' => 10,
                    'message' => $th->getMessage()
                ], 500)
            );
        }

        return true;
    }
}