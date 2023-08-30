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

use Illuminate\Support\Facades\Route;

Route::prefix('job-application')->group(function() {
    Route::get('/', fn() => response()->json([
        'success' => true,
        'result_code' => 0,
        'message' => 'Job Application Module',
    ]));
});
