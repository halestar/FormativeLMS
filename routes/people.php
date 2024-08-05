<?php

use Illuminate\Support\Facades\Route;
Route::get('/{person}', [\App\Http\Controllers\People\PersonController::class, 'show'])->name('show');
Route::get('/{person}/edit', [\App\Http\Controllers\People\PersonController::class, 'edit'])->name('edit');
Route::put('/{person}/basic', [\App\Http\Controllers\People\PersonController::class, 'updateBasic'])->name('update.basic');
