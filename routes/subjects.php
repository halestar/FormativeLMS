<?php

use App\Http\Controllers\School\StudentTrackerController;
use Illuminate\Support\Facades\Route;

//Subjects
Route::prefix('subjects')
     ->name('subjects.')
     ->controller(\App\Http\Controllers\SubjectMatter\SubjectsController::class)
     ->group(function()
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
     ->controller(\App\Http\Controllers\SubjectMatter\CourseController::class)
     ->group(function()
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
     ->controller(\App\Http\Controllers\SubjectMatter\SchoolClassController::class)
     ->group(function()
     {
	     Route::get('/{course?}', 'index')
	          ->name('index');
	     Route::post('/{course}', 'store')
	          ->name('store');
	     Route::get('/{schoolClass}/edit', 'edit')
	          ->name('edit');
	     Route::delete('/{schoolClass}', 'destroy')
	          ->name('destroy');
     });

//Class Enrollment
Route::prefix('enrollment')
     ->name('enrollment.')
     ->controller(\App\Http\Controllers\ClassManagement\StudentEnrollmentController::class)
     ->group(function()
     {
	     Route::get('/enrollment/general', 'general')
	          ->name('general');
     });

//Anything related to the school's class management system
Route::prefix('school/classes')
     ->name('school.classes.')
     ->controller(\App\Http\Controllers\School\ClassController::class)
     ->group(function()
     {
	     Route::get('/messages', 'classMessages')
	          ->name('messages');
	     Route::get('/{classSession}', 'show')
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
     ->controller(\App\Http\Controllers\ClassManagement\SkillsController::class)
     ->group(function()
     {
	     Route::get('/', 'index')
	          ->name('index');
	     Route::post('/store', 'store')
	          ->name('store');
	     Route::get('/create/{category?}', 'create')
	          ->name('create');
	     Route::get('/{skill}/rubric', 'rubric')
	          ->name('rubric');
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
