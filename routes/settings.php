<?php

use App\Http\Controllers\Admin\SchoolSettingsController;
use Illuminate\Support\Facades\Route;

Route::resource('permissions', \App\Http\Controllers\Settings\PermissionController::class)
    ->except(['show']);

Route::resource('roles', \App\Http\Controllers\Settings\RoleController::class)
    ->except(['show']);

//school Settings
Route::get('/school/settings', [SchoolSettingsController::class, 'show'])->name('school');
Route::patch('/school/settings/school', [SchoolSettingsController::class, 'update'])->name('school.update.school');
Route::patch('/school/settings/classes', [SchoolSettingsController::class, 'updateClasses'])->name('school.update.classes');
Route::patch('/school/settings/ids', [SchoolSettingsController::class, 'updateId'])->name('school.update.ids');
Route::get('/school/settings/name/{role}', [SchoolSettingsController::class, 'nameCreator'])->name('school.name');

