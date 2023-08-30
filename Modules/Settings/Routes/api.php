<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\DataController;

Route::prefix('settings')->group(function() {
    Route::get('form-options', [DataController::class, 'getFormOptions']);
});

Route::get('app/configurations', [DataController::class, 'getConfigurations']);