<?php

use Illuminate\Support\Facades\Route;
use Modules\JobApplication\Http\Controllers\Applicant\JobApplyController;


Route::post('job/apply', JobApplyController::class);