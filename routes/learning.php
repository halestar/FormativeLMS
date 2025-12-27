<?php


use App\Http\Controllers\School\ClassController;
use App\Livewire\School\ClassCriteriaManager;
use App\Livewire\School\ClassSettings;
use App\Livewire\SubjectMatter\Learning\LearningDemonstrationAssessor;
use App\Livewire\SubjectMatter\Learning\LearningDemonstrationCreator;
use App\Livewire\SubjectMatter\Learning\LearningDemonstrationEditor;
use App\Livewire\SubjectMatter\Learning\LearningDemonstrationIndex;
use App\Livewire\SubjectMatter\Learning\LearningDemonstrationPoster;
use App\Livewire\SubjectMatter\Learning\Opportunities\OpportunityDemonstrator;
use App\Livewire\SubjectMatter\Learning\Opportunities\OpportunityViewer;
use App\Models\Utilities\SchoolRoles;

Route::prefix('learning-demonstrations/opportunities')
	->name('ld.opportunities.')
	->group(function()
	{
		Route::get('/viewer/{opportunity}/{classSession}', OpportunityViewer::class)->name('viewer');
		Route::get('/demonstrator/{opportunity}/{classSession}', OpportunityDemonstrator::class)
			->name('demonstrator')
			->middleware('role:' . SchoolRoles::$STUDENT . "|" . SchoolRoles::$OLD_STUDENT);
	});

Route::prefix('classes')
	->name('classes.')
	->controller(ClassController::class)
	->group(function()
	{
		Route::get('/settings/{classSession?}', 'settings')
			->name('settings');
		Route::get('/criteria/{classSession?}', 'criteria')
			->name('criteria');
	});

Route::prefix('learning-demonstrations')
     ->name('ld.')
     ->middleware('role:' . SchoolRoles::$FACULTY . "|" . SchoolRoles::$OLD_FACULTY)
	 ->group(function()
	 {
		Route::get('/index/{course?}', LearningDemonstrationIndex::class)->name('index');
		Route::get('/create/{course?}', LearningDemonstrationCreator::class)->name('create');
	    Route::get('/post/{ld}', LearningDemonstrationPoster::class)->name('post');
		Route::get('/edit/{ld}/{classSession}', LearningDemonstrationEditor::class)->name('edit');
	    Route::get('/assess/{ld}/{classSession?}', LearningDemonstrationAssessor::class)->name('assess');
	 });


Route::get('/class-criteria/{classSession?}', ClassCriteriaManager::class)->name('criteria');






