<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Location\Http\Controllers\DataController;

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

Route::prefix('location')->group(function() {
    Route::get('/get-countries/{option?}', [DataController::class, 'countryList']);
    Route::get('/get-divisions', [DataController::class, 'divisionList']);
    Route::get('/get-districts', [DataController::class, 'districtList']);
    Route::get('/get-areas', [DataController::class, 'areaList']);
    Route::get('/get-timezones', [DataController::class, 'getTimezones']);
});