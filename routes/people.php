<?php

use Illuminate\Support\Facades\Route;


Route::prefix('policies/view')
    ->name('policies.view.')
    ->controller(\App\Http\Controllers\People\ViewPolicyController::class)
    ->group(function ()
    {
        Route::get('/personal', 'personal')->name('personal');
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/create', 'store')->name('store');
        Route::get('/{policy}/edit', 'edit')->name('edit');
        Route::put('/{policy}', 'update')->name('update');
        Route::delete('/{policy}', 'destroy')->name('destroy');
    });

Route::get('/{person}', [\App\Http\Controllers\People\PersonController::class, 'show'])->name('show');
Route::get('/{person}/edit', [\App\Http\Controllers\People\PersonController::class, 'edit'])->name('edit');
Route::put('/{person}/basic', [\App\Http\Controllers\People\PersonController::class, 'updateBasic'])->name('update.basic');

