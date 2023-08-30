<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Frontend\Http\Controllers\PageController;
use Modules\Job\Http\Controllers\Frontend\DataController;
use Modules\Job\Http\Controllers\Frontend\JobController;

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

Route::prefix('job')->group(function() {
    Route::get('/get-categories', [DataController::class, 'jobCategories']);
    Route::get('/detail/{code}', [JobController::class, 'jobDetail']);
    Route::get('/list', [JobController::class, 'activeJobs']);
});