<?php

use App\Http\Controllers\Locations\BlockController;
use App\Http\Controllers\Locations\BuildingController;
use App\Http\Controllers\Locations\CampusController;
use App\Http\Controllers\Locations\PeriodController;
use App\Http\Controllers\Locations\RoomController;
use App\Http\Controllers\Locations\YearController;
use App\Http\Resources\Locations\BuildingAreaResource;
use App\Livewire\Locations\BuildingAreaEditor;
use App\Models\Locations\BuildingArea;
use Illuminate\Support\Facades\Route;

//Campuses
Route::controller(CampusController::class)
	->prefix('campuses')
	->name('campuses.')
	->group(function()
	{
		Route::put('/{campus}/basic', [CampusController::class, 'updateBasicInfo'])
			->name('update.basic');
		Route::put('/{campus}/img', [CampusController::class, 'updateImg'])
			->name('update.img');
		Route::put('/{campus}/icon', [CampusController::class, 'updateIcon'])
			->name('update.icon');
		Route::put('/{campus}/levels', [CampusController::class, 'updateLevels'])
			->name('update.levels');
		Route::get('/{campus}/order/{order}', [CampusController::class, 'updateOrder'])
			->name('update.order');
	});
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
Route::controller(RoomController::class)
	->prefix('rooms')
	->name('rooms.')
	->group(function()
	{
		Route::put('/{room}/basic', 'updateBasicInfo')
			->name('update.basic');
		Route::put('/{room}/campuses', 'updateCampuses')
			->name('update.campuses');
		Route::get('/create/{building?}', 'create')
			->name('create');
	});
Route::resource('rooms', RoomController::class)
     ->except(['create', 'index']);

//periods
Route::name('periods.')
	->prefix('periods')
     ->controller(PeriodController::class)
     ->group(function()
     {
	     Route::get('/{campus}/create', 'create')
	          ->name('create');
	     Route::post('/{campus}/create', 'store')
	          ->name('store');
	     Route::get('/{period}/edit', 'edit')
	          ->name('edit');
	     Route::put('/{period}', 'update')
	          ->name('update');
	     Route::delete('/{period}', 'destroy')
	          ->name('destroy');
	     Route::get('/{campus}/edit/mass', 'massEdit')
	          ->name('edit.mass');
     });

//blocks
Route::name('blocks.')
	->prefix('blocks')
    ->controller(BlockController::class)
    ->group(function()
     {
	     Route::put('/order', 'updateOrder')
	          ->name('update.order');
	     Route::post('/{campus}/create', 'store')
	          ->name('store');
	     Route::get('/{block}/edit', 'edit')
	          ->name('edit');
	     Route::put('/{block}', 'update')
	          ->name('update');
	     Route::delete('/{block}', 'destroy')
	          ->name('destroy');
	     Route::put('/order', 'updateOrder')
	          ->name('update.order');
     });
