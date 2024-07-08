<?php

use Illuminate\Support\Facades\Route;

Route::resource('permissions', \App\Http\Controllers\Settings\PermissionController::class)
    ->except(['show']);

Route::resource('roles', \App\Http\Controllers\Settings\RoleController::class)
    ->except(['show']);
