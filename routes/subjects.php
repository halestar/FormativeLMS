<?php

use Illuminate\Support\Facades\Route;

//Subjects
Route::prefix('subjects')
    ->name('subjects.')
    ->controller(\App\Http\Controllers\SubjectMatter\SubjectsController::class)
    ->group(function () {
        Route::get('/{campus?}', 'index')->name('index');
        Route::post('/{campus}', 'store')->name('store');
        Route::get('/{subject}/edit', 'edit')->name('edit');
        Route::put('/order', 'updateOrder')->name('update.order');
        Route::put('/{subject}', 'update')->name('update');
        Route::delete('/{subject}', 'destroy')->name('destroy');
    });

//Courses
Route::prefix('courses')
    ->name('courses.')
    ->controller(\App\Http\Controllers\SubjectMatter\CourseController::class)
    ->group(function () {
        Route::get('/{subject}', 'index')->name('index');
        Route::post('/{subject}', 'store')->name('store');
        Route::get('/{course}/edit', 'edit')->name('edit');
        Route::put('/{course}', 'update')->name('update');
        Route::delete('/{course}', 'destroy')->name('destroy');
    });
