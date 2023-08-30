<?php

namespace Modules\User\Traits;

use App\Services\FileUpload\FileUpload;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\User\Http\Resources\UserInformation;
use Modules\User\Models\User;

trait LoginUsers {

    /**
     * Process User Login Request
     *
     * @return Responsable
     * @throws HttpResponseException If login credentials mismatched
     */
    public function login(Request $request)
    {
        $this->loginRequestValidation($request);

        $rememberMe = $request->has('remember') ? isTrue($request->remember) : false;

        $credentials = $request->only(['username', 'password']);

        if(filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $request->username;
            unset($credentials['username']);
        } else {
            $credentials['mobile'] = $request->username;
            unset($credentials['username']);
        }

        if (!Auth::attempt($credentials, $rememberMe)) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'result_code' => 10,
                    'message' => 'Login credentials mismatched'
                ], 401)
            );
        }

        // $user->last_login_at = now();
        // $user->save();

        $user = User::find(auth('sanctum')->id());

        return $this->success()
            ->message('Login successful',)
            ->response([
                'token' => $user->createToken('authToken')->plainTextToken,
                'user_type' => $user->user_type,
                'user' => new UserInformation($user),
            ]);

    }

    /**
     * Validate login request
     *
     * @param Request $request
     * @return void
     * @throws HttpResponseException If request validation fails
     */
    private function loginRequestValidation(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|string',
                'password' => 'required'
            ]
        );

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'result_code' => 10,
                    'message' => $validator->errors()->first()
                ])
            );
        }
    }

}