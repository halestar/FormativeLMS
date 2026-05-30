<?php

use App\Http\Controllers\Substitutes\SubstituteController;
use App\Http\Controllers\Substitutes\SubstituteRequestController;

Route::prefix('substitutes')
     ->name('substitutes.')
     ->group(function ()
     {
	     Route::get('/', [SubstituteRequestController::class, 'index'])->name('index');
	     Route::livewire('/create', 'pages::substitutes.create')->name('create');
	     Route::livewire('/matrix', 'pages::substitutes.matrix')->name('matrix');
	     Route::prefix('pool')->name('pool.')->group(function ()
	     {

		     Route::get('/', [SubstituteController::class, 'index'])->name('index');
		     Route::livewire('/create', 'pages::substitutes.pool.create')->name('create');
		     Route::livewire('/{person}', 'pages::substitutes.show')->name('show');
	     });
	     Route::livewire('/{subRequest}', 'pages::substitutes.requests')->name('show');
     });
