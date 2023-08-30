<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Frontend\Http\Controllers\DataController;
use Modules\Frontend\Http\Controllers\PageController;

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

Route::prefix('frontend')->group(function() {
    Route::get('/home', [PageController::class, 'getHome']);
    Route::get('/section/{name?}', [PageController::class, 'getSections']);
    Route::get('/get-layouts', [PageController::class, 'getLayouts']);
    Route::get('/job-wings', [PageController::class, 'getJobWings']);
    Route::get('/job-cities', [PageController::class, 'getJobCities']);
    Route::get('/job-functions', [DataController::class, 'getJobFunctions']);
    Route::get('/special-skills', [DataController::class, 'getSpecialSkills']);
    Route::get('/educations', [DataController::class, 'getEducations']);
});