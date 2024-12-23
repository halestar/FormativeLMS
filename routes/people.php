<?php

use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\People\PersonController::class)
    ->group(function ()
    {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/roles/fields', 'roleFields')->name('roles.fields');
        Route::get('/{person}', 'show')->name('show');
        Route::get('/{person}/edit', 'edit')->name('edit');
        Route::put('/{person}/basic','updateBasic')->name('update.basic');
        Route::post('/{person}/portrait', 'updatePortrait')->name('update.portrait');
        Route::delete('/{person}/portrait', 'deletePortrait')->name('delete.portrait');
        Route::put('/{person}/fields/{role}/update', 'updateRoleFields')->name('roles.fields.update');

        Route::get('/fields/permissions', 'fieldPermissions')->name('fields.permissions');
    });

