<?php

use App\Http\Controllers\Settings\PermissionController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\SchoolSettingsController;
use App\Http\Controllers\Settings\SystemTablesController;
use App\Http\Controllers\Storage\StorageController;
use App\Livewire\Utilities\SchoolMessageEditor;
use Illuminate\Support\Facades\Route;

/********************************************************************
 * PERMISSIONS ROUTES
 */
Route::resource('permissions', PermissionController::class)
     ->except(['show']);
Route::resource('roles', RoleController::class)
     ->except(['show']);

/********************************************************************
 * SYSTEM TABLES ROUTES
 */
Route::get('/system/tables', [SystemTablesController::class, 'index'])
     ->name('system.tables');

Route::prefix('/school/settings')
	->group(function ()
	{
		//school Settings
		Route::get('/', [SchoolSettingsController::class, 'show'])
			->name('school');
		Route::controller(SchoolSettingsController::class)
			->name('school.')
			->group(function()
			{
				Route::patch('/school', 'update')
					->name('update.school');
				Route::patch('/classes', 'updateClasses')
					->name('update.classes');
				Route::patch('/ids', 'updateId')
					->name('update.ids');
				Route::patch('/auth', 'updateAuth')
					->name('update.auth');
				Route::patch('storage', 'updateStorage')
					->name('update.storage');
				Route::patch('/communications', 'updateCommunications')
					->name('update.communications');
				Route::get('/name/{role}', 'nameCreator')
					->name('name');
			});

		Route::livewire('/messages/{message}', SchoolMessageEditor::class)
			->name('school.messages')
			->middleware('can:school.emails');
	});





//storage settings
Route::controller(StorageController::class)
	->prefix('work-files')
	->name('work.')
	->group(function()
	{
		Route::get('/{work_file}', 'downloadWorkFile')
			->name('file')
			->whereUuid('work_file');

		Route::get('/thumb/{work_file}', 'downloadWorkFileThumb')
			->name('file.thumb')
			->whereUuid('work_file');
	});


