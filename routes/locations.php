<?php

use App\Http\Controllers\Locations\AreaController;
use App\Http\Controllers\Locations\BuildingController;
use App\Http\Controllers\Locations\CampusController;
use App\Http\Controllers\Locations\RoomController;
use App\Http\Controllers\Locations\YearController;
use Illuminate\Support\Facades\Route;

//Campuses
Route::put('/campuses/{campus}/basic', [CampusController::class, 'updateBasicInfo'])->name('campuses.update.basic');
Route::put('/campuses/{campus}/img', [CampusController::class, 'updateImg'])->name('campuses.update.img');
Route::put('/campuses/{campus}/icon', [CampusController::class, 'updateIcon'])->name('campuses.update.icon');
Route::put('/campuses/{campus}/levels', [CampusController::class, 'updateLevels'])->name('campuses.update.levels');
Route::get('/campuses/{campus}/order/{order}', [CampusController::class, 'updateOrder'])->name('campuses.update.order');
Route::resource('campuses', CampusController::class)->except(['update', 'create']);

//Years and Terms
Route::post('/years/{year}/terms/create', [YearController::class, 'storeTerm'])->name('years.terms.store');
Route::resource('years', YearController::class)->except(['create', 'edit']);

//Buildings
Route::put('/buildings/{building}/basic', [BuildingController::class, 'updateBasicInfo'])->name('buildings.update.basic');
Route::put('/buildings/{building}/img', [BuildingController::class, 'updateImg'])->name('buildings.update.img');
Route::put('/buildings/{building}/areas', [BuildingController::class, 'updateAreas'])->name('buildings.update.areas');
Route::resource('buildings', BuildingController::class)->except(['create', 'update']);

//Building Areas
Route::get('/maps/area/{area}', [AreaController::class, 'areaMap'])
    ->name('maps.area')
    ->middleware('can:locations.area');
Route::resource('areas', AreaController::class)->only('show');

//Rooms
Route::put('/rooms/{room}/basic', [RoomController::class, 'updateBasicInfo'])->name('rooms.update.basic');
Route::put('/rooms/{room}/campuses', [RoomController::class, 'updateCampuses'])->name('rooms.update.campuses');
Route::get('/rooms/create/{building?}', [RoomController::class, 'create'])->name('rooms.create');
Route::resource('rooms', RoomController::class)->except(['create','index']);