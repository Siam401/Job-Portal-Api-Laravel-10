<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\AuthController;
use Modules\User\Models\User;
use Modules\User\Notifications\EmailVerification;

Route::prefix('user')->group(function () {
    Route::get('/', function () {
        Notification::send(User::first(), new EmailVerification());
        return response()->json([
            'success' => true,
            'result_code' => 0,
            'message' => 'User Module',
        ]);
    });
});

Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'processVerification'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [AuthController::class, 'sendVerification'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');