<?php

use Illuminate\Support\Facades\Route;

Route::prefix('location')->group(function() {
    Route::get('/', fn() => response()->json([
        'success' => true,
        'result_code' => 0,
        'message' => 'Location Module',
    ]));
});