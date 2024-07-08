<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\CMS\FrontController::class, 'index']);
Route::prefix('cms')->name('cms.')->group(function()
{
    Route::resource('posts', \App\Http\Controllers\CMS\BlogPostController::class)->except('show');
});
Route::get('/cms/posts/{post}', [\App\Http\Controllers\CMS\BlogPostController::class, 'show'])->name('blog.show');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


