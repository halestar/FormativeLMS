<?php

use App\Http\Controllers\Settings\SchoolSettingsController;
use App\Http\Controllers\Settings\SystemTablesController;
use App\Livewire\Utilities\SchoolMessageEditor;
use Illuminate\Support\Facades\Route;

/********************************************************************
 * PERMISSIONS ROUTES
 */
Route::resource('permissions', \App\Http\Controllers\Settings\PermissionController::class)
     ->except(['show']);
Route::resource('roles', \App\Http\Controllers\Settings\RoleController::class)
     ->except(['show']);

/********************************************************************
 * SYSTEM TABLES ROUTES
 */
Route::get('/system/tables', [SystemTablesController::class, 'index'])
     ->name('system.tables');

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
Route::patch('/school/settings/communications', [SchoolSettingsController::class, 'updateCommunications'])
    ->name('school.update.communications');

Route::get('/school/settings/name/{role}', [SchoolSettingsController::class, 'nameCreator'])
     ->name('school.name');
Route::get('/school/settings/messages/{message}', SchoolMessageEditor::class)
     ->name('school.messages')
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

