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

Route::controller(\App\Http\Controllers\People\IdController::class)
    ->name('school-ids.')
    ->prefix('school-ids')
    ->group(function ()
    {
        Route::get('/mine', 'show')->name('show');
        Route::get('/global', 'manageGlobal')->name('manage.global');
        Route::post('/global', 'updateGlobal')->name('manage.global.update');
        Route::get('/role/{role}', 'manageRole')->name('manage.role');
        Route::post('/role/{role}', 'updateRole')->name('manage.role.update');
        Route::get('/campus/{campus}', 'manageCampus')->name('manage.campus');
        Route::post('/campus/{campus}', 'updateCampus')->name('manage.campus.update');
        Route::get('/both/{role}/{campus}', 'manageRoleCampus')->name('manage.both');
        Route::post('/both/{role}/{campus}', 'updateRoleCampus')->name('manage.both.update');
    });

