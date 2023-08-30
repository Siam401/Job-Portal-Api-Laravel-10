<?php

namespace Modules\User\Traits;

use Illuminate\Http\Request;

trait LogoutUsers {
/**
     * Process User Logout Request
     *
     * @return Responsable
     */
    public function logout(Request $request)
    {

        $user = request()->user();
        if ($request->all) {
            $user->tokens()->delete();
        } else if ($request->token) {
            $user->tokens()->where('id', $request->token)->delete();
        } else {
            // Revoke current user token
            $user->currentAccessToken()->delete();
        }

        return $this->success()
            ->message('User successfully logged out')
            ->response();
    }
}