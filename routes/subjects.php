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
        Route::get('/{subject?}', 'index')->name('index');
        Route::post('/{subject}', 'store')->name('store');
        Route::get('/{course}/edit', 'edit')->name('edit');
        Route::put('/{course}', 'update')->name('update');
        Route::delete('/{course}', 'destroy')->name('destroy');
    });

//School Class Management
Route::prefix('classes')
    ->name('classes.')
    ->controller(\App\Http\Controllers\SubjectMatter\SchoolClassController::class)
    ->group(function ()
    {
        Route::get('/{course?}', 'index')->name('index');
        Route::post('/{course}', 'store')->name('store');
        Route::get('/{schoolClass}/edit', 'edit')->name('edit');
        Route::delete('/{schoolClass}', 'destroy')->name('destroy');
    });

//Class Enrollment
Route::prefix('enrollment')
    ->name('enrollment.')
    ->controller(\App\Http\Controllers\ClassManagement\StudentEnrollmentController::class)
    ->group(function ()
    {
        Route::get('/enrollment/general', 'general')->name('general');
    });
