<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\ApplicantNotificationController;

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

Route::middleware(['auth:sanctum'])->prefix('applicant')->group(function () {
    Route::get('/notifications', [ApplicantNotificationController::class, 'index']);
    Route::get('/notifications/{notification}', [ApplicantNotificationController::class, 'getDetail']);
    Route::delete('/notifications/{notification}', [ApplicantNotificationController::class, 'removeNotification']);
    // Route::get('/notifications/{notification}/mark-as-read', [ApplicantNotificationController::class, 'markAsRead']);
});