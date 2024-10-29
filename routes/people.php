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

Route::controller(\App\Http\Controllers\People\PersonController::class)
    ->group(function ()
    {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/{person}', 'show')->name('show');
        Route::get('/{person}/edit', 'edit')->name('edit');
        Route::put('/{person}/basic','updateBasic')->name('update.basic');
        Route::post('/{person}/portrait', 'updatePortrait')->name('update.portrait');
        Route::delete('/{person}/portrait', 'deletePortrait')->name('delete.portrait');
    });

