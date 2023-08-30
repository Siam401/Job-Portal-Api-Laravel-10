<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'result_code' => 0,
        'message' => 'Server is running',
        'data' => [
            'text' => 'Welcome'
        ]
    ]);
});
