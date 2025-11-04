<?php


use App\Livewire\SubjectMatter\Learning\ClassCriteriaManager;
use App\Livewire\SubjectMatter\Learning\LearningDemonstrationCreator;
use App\Livewire\SubjectMatter\Learning\LearningDemonstrationIndex;
use App\Livewire\SubjectMatter\Learning\LearningDemonstrationPoster;
use App\Models\Utilities\SchoolRoles;

Route::prefix('learning-demonstrations')
     ->name('ld.')
     ->middleware('role:' . SchoolRoles::$FACULTY . "|" . SchoolRoles::$OLD_FACULTY)
	 ->group(function()
	 {
		Route::get('/index/{course?}', LearningDemonstrationIndex::class)->name('index');
		Route::get('/create/{course?}', LearningDemonstrationCreator::class)->name('create');
	    Route::get('/post/{ld}', LearningDemonstrationPoster::class)->name('post');
	 });

Route::get('/class-criteria', ClassCriteriaManager::class)->name('criteria');





