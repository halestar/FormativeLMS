<?php

use App\Http\Controllers\Admin\CrudController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

//Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
//callback routes
foreach(config('auth.drivers') as $driver)
{
	if(($driver['class'])::requiresRedirection())
		Route::get("/auth/" . ($driver['class'])::driverName() . "/callback", [$driver['class'], 'callback']);
}

//auth pages
Route::get('/home', [HomeController::class, 'index'])->name('home');

//cms
Route::prefix('cms')->middleware(['can:cms', 'auth'])->group(function()
{
    \halestar\LaravelDropInCms\DiCMS::adminRoutes();
});

//crud
Route::get('/crud', [CrudController::class, 'index'])->name('crud');

//settings
Route::post('/settings', [HomeController::class, 'setSessionSetting']);
Route::get('/settings', [HomeController::class, 'getSessionSetting']);






