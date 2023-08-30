<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Applicant\Http\Controllers\Applicant\DashboardController;
use Modules\Applicant\Http\Controllers\Applicant\InformationController;
use Modules\Applicant\Http\Controllers\Applicant\ResumeController;

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
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/job-applications/{type}', [DashboardController::class, 'getJobApplications']);
    Route::delete('/job-application/{application}', [DashboardController::class, 'removeApplication']);

    Route::get('/resume-status', [ResumeController::class, 'getResumeStatus']);
    Route::get('/view-resume', [ResumeController::class, 'index']);
    Route::get('/get-resume', [ResumeController::class, 'getFile']);
    Route::post('/save-resume', [ResumeController::class, 'saveFile']);
    Route::delete('/remove-resume', [ResumeController::class, 'removeFile']);

    Route::prefix('information')->group(function () {
        // Route::redirect('/', '/personal', 301);
        Route::get('/get/{category?}', [InformationController::class, 'index'])->where('category', '[a-z]+');
        Route::post('/save', [InformationController::class, 'saveInformation']);
        Route::delete('/remove/{category}/{id}', [InformationController::class, 'remove'])->where(['id' => '[0-9]+', 'category' => '[a-z]+']);
    });
});
