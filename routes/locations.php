<?php

use App\Http\Controllers\Locations\AreaController;
use App\Http\Controllers\Locations\BuildingController;
use App\Http\Controllers\Locations\CampusController;
use App\Http\Controllers\Locations\RoomController;
use App\Http\Controllers\Locations\YearController;
use App\Http\Resources\Locations\BuildingAreaResource;
use App\Livewire\Locations\BuildingAreaEditor;
use App\Models\Locations\BuildingArea;
use Illuminate\Support\Facades\Route;

//Campuses
Route::put('/campuses/{campus}/basic', [CampusController::class, 'updateBasicInfo'])
     ->name('campuses.update.basic');
Route::put('/campuses/{campus}/img', [CampusController::class, 'updateImg'])
     ->name('campuses.update.img');
Route::put('/campuses/{campus}/icon', [CampusController::class, 'updateIcon'])
     ->name('campuses.update.icon');
Route::put('/campuses/{campus}/levels', [CampusController::class, 'updateLevels'])
     ->name('campuses.update.levels');
Route::get('/campuses/{campus}/order/{order}', [CampusController::class, 'updateOrder'])
     ->name('campuses.update.order');
Route::resource('campuses', CampusController::class)
     ->except(['update', 'create']);

//Years and Terms
Route::post('/years/{year}/terms/create', [YearController::class, 'storeTerm'])
     ->name('years.terms.store');
Route::resource('years', YearController::class)
     ->except(['create', 'edit']);

//Buildings
Route::prefix('/buildings/{building}')
	->name('buildings.update.')
	->controller(BuildingController::class)
	->group(function()
	{
		Route::put('/basic', 'updateBasicInfo')
			->name('basic');
		Route::put('/img', 'updateImg')
			->name('img');
		Route::put('/areas', 'updateAreas')
			->name('areas');
		Route::put('/areas/{area}/map', 'updateMap')
			->name('areas.map');
	});

Route::resource('buildings', BuildingController::class)
     ->except(['create', 'update']);

//Building Areas
Route::get('/areas/map/{area}', fn(BuildingArea $area) => new BuildingAreaResource($area))
     ->name('areas.map');
Route::get('/areas/{area}', BuildingAreaEditor::class)
	->name('areas.show');

//Rooms
Route::put('/rooms/{room}/basic', [RoomController::class, 'updateBasicInfo'])
     ->name('rooms.update.basic');
Route::put('/rooms/{room}/campuses', [RoomController::class, 'updateCampuses'])
     ->name('rooms.update.campuses');
Route::get('/rooms/create/{building?}', [RoomController::class, 'create'])
     ->name('rooms.create');
Route::resource('rooms', RoomController::class)
     ->except(['create', 'index']);

//periods
Route::name('periods.')
     ->controller(\App\Http\Controllers\Locations\PeriodController::class)
     ->group(function()
     {
	     Route::get('/periods/{campus}/create', 'create')
	          ->name('create');
	     Route::post('/periods/{campus}/create', 'store')
	          ->name('store');
	     Route::get('/periods/{period}/edit', 'edit')
	          ->name('edit');
	     Route::put('/periods/{period}', 'update')
	          ->name('update');
	     Route::delete('/periods/{period}', 'destroy')
	          ->name('destroy');
	     Route::get('/periods/{campus}/edit/mass', 'massEdit')
	          ->name('edit.mass');
     });

//blocks
Route::name('blocks.')
     ->controller(\App\Http\Controllers\Locations\BlockController::class)
     ->group(function()
     {
	     Route::put('/blocks/order', 'updateOrder')
	          ->name('update.order');
	     Route::post('/blocks/{campus}/create', 'store')
	          ->name('store');
	     Route::get('/blocks/{block}/edit', 'edit')
	          ->name('edit');
	     Route::put('/blocks/{block}', 'update')
	          ->name('update');
	     Route::delete('/blocks/{block}', 'destroy')
	          ->name('destroy');
	     Route::put('/blocks/order', 'updateOrder')
	          ->name('update.order');
     });
