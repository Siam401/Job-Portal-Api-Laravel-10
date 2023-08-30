<?php

use Illuminate\Support\Facades\Route;

Route::prefix('job-interview')->group(function() {
    Route::get('/', fn() => response()->json([
        'success' => true,
        'result_code' => 0,
        'message' => 'Job Interview Module',
    ]));
});
