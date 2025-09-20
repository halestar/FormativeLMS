<?php

use App\Http\Controllers\Settings\SchoolSettingsController;
use App\Livewire\Utilities\SchoolEmailsEditor;
use Illuminate\Support\Facades\Route;

Route::resource('permissions', \App\Http\Controllers\Settings\PermissionController::class)
     ->except(['show']);

Route::resource('roles', \App\Http\Controllers\Settings\RoleController::class)
     ->except(['show']);

//school Settings
Route::get('/school/settings', [SchoolSettingsController::class, 'show'])
     ->name('school');
Route::patch('/school/settings/school', [SchoolSettingsController::class, 'update'])
     ->name('school.update.school');
Route::patch('/school/settings/classes', [SchoolSettingsController::class, 'updateClasses'])
     ->name('school.update.classes');
Route::patch('/school/settings/ids', [SchoolSettingsController::class, 'updateId'])
     ->name('school.update.ids');
Route::patch('/school/settings/auth', [SchoolSettingsController::class, 'updateAuth'])
     ->name('school.update.auth');
Route::patch('/school/settings/storage', [SchoolSettingsController::class, 'updateStorage'])
     ->name('school.update.storage');
Route::get('/school/settings/name/{role}', [SchoolSettingsController::class, 'nameCreator'])
     ->name('school.name');
Route::get('/school/settings/emails', SchoolEmailsEditor::class)
     ->name('school.emails')
     ->middleware('can:school.emails');

//storage settings
Route::get('/work-files/private/{work_file}',
	[\App\Http\Controllers\Storage\StorageController::class, 'downloadWorkFile'])
     ->name('work.file.private')
     ->whereUuid('work_file');

Route::get('/work-files/{work_file}',
	[\App\Http\Controllers\Storage\StorageController::class, 'downloadPublicWorkFile'])
     ->name('work.file.public')
     ->whereUuid('work_file');