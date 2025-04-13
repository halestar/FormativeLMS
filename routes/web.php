<?php

use App\Http\Controllers\Admin\CrudController;
use App\Http\Controllers\Admin\SchoolSettingsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

//Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//auth pages
Route::get('/home', [HomeController::class, 'index'])->name('home');

//cms
Route::prefix('cms')->middleware(['can:cms', 'auth'])->group(function()
{
    \halestar\LaravelDropInCms\DiCMS::adminRoutes();
});

//crud
Route::get('/crud', [CrudController::class, 'index'])->name('crud');

//school Settings
Route::get('/school/settings', [SchoolSettingsController::class, 'show'])->name('school.settings');
Route::patch('/school/settings/school', [SchoolSettingsController::class, 'update'])->name('school.settings.update.school');
Route::patch('/school/settings/classes', [SchoolSettingsController::class, 'updateClasses'])->name('school.settings.update.classes');
Route::get('/school/settings/name/{role}', [SchoolSettingsController::class, 'nameCreator'])->name('school.settings.name');

//settings
Route::post('/settings', [HomeController::class, 'setSessionSetting']);
Route::get('/settings', [HomeController::class, 'getSessionSetting']);






