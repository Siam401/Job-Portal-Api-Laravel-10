<?php

use App\Http\Controllers\Mock\CompanyController;
use App\Http\Controllers\Mock\FrontendController;
use App\Http\Controllers\Mock\IndexController;
use App\Http\Controllers\Mock\JobController;
use App\Http\Controllers\Mock\JobQuestionController;
use App\Http\Controllers\Mock\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mock', IndexController::class)->name('mock');
// Route::get('/mock/test', function () {
//     return view('mock.section.test');
// });

Route::prefix('mock')->as('mock.')->group(function () {
    Route::resource('job', JobController::class);
    Route::get('job-questions', [JobQuestionController::class, 'index'])->name('job-questions.index');
    Route::post('job-questions', [JobQuestionController::class, 'save'])->name('job-questions.save');
    Route::resource('company', CompanyController::class);
    Route::get('frontend', [FrontendController::class, 'index'])->name('frontend');
    Route::post('frontend/update', [FrontendController::class, 'update'])->name('frontend.update');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::any('settings/social-links', [SettingsController::class, 'socialLinks'])->name('settings.social-links');
    Route::any('settings/social-auths', [SettingsController::class, 'socialAuths'])->name('settings.social-auths');
    Route::get('settings/environment', [SettingsController::class, 'getEnvironment'])->name('settings.environment.index');
    Route::post('settings/environment', [SettingsController::class, 'saveEnvironment'])->name('settings.environment.save');
});