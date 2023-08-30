<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Company\Http\Controllers\DataController;
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

Route::prefix('company')->group(function() {
    Route::get('/get-wings', [DataController::class, 'getWings']);
});