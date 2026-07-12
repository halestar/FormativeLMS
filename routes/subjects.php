<?php

use App\Http\Controllers\ClassManagement\SkillsController;
use App\Http\Controllers\School\ClassController;
use App\Http\Controllers\School\StudentTrackerController;
use App\Http\Controllers\SubjectMatter\CourseController;
use App\Http\Controllers\SubjectMatter\SchoolClassController;
use App\Http\Controllers\SubjectMatter\SubjectsController;
use App\Livewire\School\ClassMessages\MyMessages;
use Illuminate\Support\Facades\Route;

//Subjects
Route::prefix('subjects')
     ->name('subjects.')
     ->controller(SubjectsController::class)
     ->group(function ()
     {
	     Route::get('/{campus?}', 'index')
	          ->name('index');
	     Route::post('/{campus}', 'store')
	          ->name('store');
	     Route::get('/{subject}/edit', 'edit')
	          ->name('edit');
	     Route::put('/order', 'updateOrder')
	          ->name('update.order');
	     Route::put('/{subject}', 'update')
	          ->name('update');
	     Route::delete('/{subject}', 'destroy')
	          ->name('destroy');
     });

//Courses
Route::prefix('courses')
     ->name('courses.')
     ->controller(CourseController::class)
     ->group(function ()
     {
	     Route::get('/{subject?}', 'index')
	          ->name('index');
	     Route::post('/{subject}', 'store')
	          ->name('store');
	     Route::get('/{course}/edit', 'edit')
	          ->name('edit');
	     Route::put('/{course}', 'update')
	          ->name('update');
	     Route::delete('/{course}', 'destroy')
	          ->name('destroy');
     });

//School Class Management
Route::prefix('classes')
     ->name('classes.')
     ->controller(SchoolClassController::class)
     ->group(function ()
     {
	     Route::get('/{course?}', 'index')
	          ->name('index');
	     Route::post('/{course}', 'store')
	          ->name('store');
	     Route::livewire('/{schoolClass}/edit', 'pages::subject-matter.school-class-manager')
	          ->name('edit');
	     Route::delete('/{schoolClass}', 'destroy')
	          ->name('destroy');
     });

//Class Enrollment
Route::livewire('enrollment/general', 'subject-matter.general-class-enrollment')
     ->name('enrollment.general');


//Anything related to the school's class management system
Route::prefix('school/classes')
     ->name('school.classes.')
     ->group(function ()
     {
	     Route::livewire('/messages', MyMessages::class)
	          ->name('messages');
	     Route::get('/{classSession}', [ClassController::class, 'show'])
	          ->name('show');
     });

//student tracking
Route::delete('/student-tracker/{student_tracker}/unlink/{student}', [StudentTrackerController::class, 'unlink'])
     ->name('student-tracker.unlink');
Route::resource('student-tracker', StudentTrackerController::class)
     ->only(['index', 'edit', 'update', 'destroy']);

//skill management
Route::prefix('skills')
     ->name('skills.')
     ->group(function ()
     {
	     Route::livewire('/', 'assessment.skill-category-browser')->name('index');
	     Route::livewire('/{skill}/rubric', 'assessment.rubric-builder')->name('rubric');
	     Route::controller(SkillsController::class)
	          ->group(function ()
	          {
		          Route::post('/store', 'store')
		               ->name('store');
		          Route::get('/create/{category?}', 'create')
		               ->name('create');
		          Route::get('/{skill}/edit', 'edit')
		               ->name('edit');
		          Route::put('/{skill}/update', 'update')
		               ->name('update');
		          Route::delete('/{skill}/unlink/{category}', 'unlinkCategory')
		               ->name('unlink');
		          Route::post('/{skill}/link', 'linkCategory')
		               ->name('link');
		          Route::get('/{skill}/subject/{subject}', 'linkSubject')
		               ->name('link.subject');
		          Route::delete('/{skill}/subject/{subject}', 'unlinkSubject')
		               ->name('unlink.subject');
		          Route::get('/{skill}', 'show')
		               ->name('show');
		          Route::delete('/{skill}', 'destroy')->name('delete');
	          });

     });
