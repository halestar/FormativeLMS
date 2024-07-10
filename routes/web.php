<?php

use Illuminate\Support\Facades\Route;

//unauth pages
Route::get('/', [\App\Http\Controllers\CMS\FrontController::class, 'index']);
Route::get('/scheduler', function()
{
    \Illuminate\Support\Facades\Artisan::call('schedule:run');
});
Route::get('/cms/posts/show/{post}', [\App\Http\Controllers\CMS\BlogPostController::class, 'show'])->name('blog.show');

//Auth Routes
Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

//auth pages
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//cms
Route::prefix('cms')->name('cms.')->middleware('can:cms')->group(function()
{
    Route::resource('posts', \App\Http\Controllers\CMS\BlogPostController::class)->except('show');
    Route::post('/posts/upload', [\App\Http\Controllers\CMS\BlogPostController::class, 'upload'])->name('posts.upload');
    Route::get('/posts/list', [\App\Http\Controllers\CMS\BlogPostController::class, 'listImgs'])->name('posts.list');
});




