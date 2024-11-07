<?php

use App\Http\Controllers\Admin\CrudController;
use Illuminate\Support\Facades\Route;

//Auth Routes
Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

//auth pages
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//cms
Route::prefix('cms')->middleware(['can:cms', 'auth'])->group(function()
{
    \halestar\LaravelDropInCms\DiCMS::adminRoutes();
});

//crud
Route::get('/crud', [CrudController::class, 'index'])->name('crud');




