<?php

use App\Http\Controllers\Locations\CampusController;
use Illuminate\Support\Facades\Route;

//Campuses
Route::put('/campuses/{campus}/basic', [CampusController::class, 'updateBasicInfo'])->name('campuses.update.basic');
Route::put('/campuses/{campus}/address', [CampusController::class, 'updateAddress'])->name('campuses.update.address');
Route::put('/campuses/{campus}/img', [CampusController::class, 'updateImg'])->name('campuses.update.img');
Route::put('/campuses/{campus}/icon', [CampusController::class, 'updateIcon'])->name('campuses.update.icon');
Route::put('/campuses/{campus}/levels', [CampusController::class, 'updateLevels'])->name('campuses.update.levels');
Route::get('/campuses/{campus}/order/{order}', [CampusController::class, 'updateOrder'])->name('campuses.update.order');
Route::resource('campuses', CampusController::class)->except(['update', 'create']);

