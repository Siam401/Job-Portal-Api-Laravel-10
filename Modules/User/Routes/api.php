<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\AuthController;
use Modules\User\Http\Controllers\UserProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group([
    'middleware' => [
        'throttle:3,1', // Every minute 3 requests only
        'guest'
    ],
    'prefix' => 'auth'
], function () {
    Route::post('/register', [AuthController::class, 'register'])->name('user.register');
    Route::post('/login', [AuthController::class, 'login'])->name('user.login');
    Route::post('/social-login', [AuthController::class, 'socialLogin'])->name('user.login.social');

    Route::post('/forget-password', [AuthController::class, 'forgetPassword'])->name('user.password.forget');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('user.verify-otp');
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('user.resend-otp');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('user.password.reset');
});

Route::middleware(['auth:sanctum'])->prefix('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    Route::get('/information', [UserProfileController::class, 'getInformation']);
    Route::put('/photo', [UserProfileController::class, 'updatePhoto']);
    Route::delete('/photo', [UserProfileController::class, 'deletePhoto']);

    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [UserProfileController::class, 'changePassword']);

});

Route::middleware(['auth:sanctum'])->prefix('applicant')->group(function () {
    Route::get('/user', [UserProfileController::class, 'getInformation']);
});