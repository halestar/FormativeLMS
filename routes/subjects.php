<?php

use App\Http\Controllers\School\StudentTrackerController;
use Illuminate\Support\Facades\Route;

//Subjects
Route::prefix('subjects')
    ->name('subjects.')
    ->controller(\App\Http\Controllers\SubjectMatter\SubjectsController::class)
    ->group(function () {
        Route::get('/{campus?}', 'index')->name('index');
        Route::post('/{campus}', 'store')->name('store');
        Route::get('/{subject}/edit', 'edit')->name('edit');
        Route::put('/order', 'updateOrder')->name('update.order');
        Route::put('/{subject}', 'update')->name('update');
        Route::delete('/{subject}', 'destroy')->name('destroy');
    });

//Courses
Route::prefix('courses')
    ->name('courses.')
    ->controller(\App\Http\Controllers\SubjectMatter\CourseController::class)
    ->group(function () {
        Route::get('/{subject?}', 'index')->name('index');
        Route::post('/{subject}', 'store')->name('store');
        Route::get('/{course}/edit', 'edit')->name('edit');
        Route::put('/{course}', 'update')->name('update');
        Route::delete('/{course}', 'destroy')->name('destroy');
    });

//School Class Management
Route::prefix('classes')
    ->name('classes.')
    ->controller(\App\Http\Controllers\SubjectMatter\SchoolClassController::class)
    ->group(function ()
    {
        Route::get('/{course?}', 'index')->name('index');
        Route::post('/{course}', 'store')->name('store');
        Route::get('/{schoolClass}/edit', 'edit')->name('edit');
        Route::delete('/{schoolClass}', 'destroy')->name('destroy');
    });

//Class Enrollment
Route::prefix('enrollment')
    ->name('enrollment.')
    ->controller(\App\Http\Controllers\ClassManagement\StudentEnrollmentController::class)
    ->group(function ()
    {
        Route::get('/enrollment/general', 'general')->name('general');
    });

//Anything related to the school's class management system
Route::prefix('school/classes')
    ->name('school.classes.')
    ->controller(\App\Http\Controllers\School\ClassController::class)
    ->group(function ()
    {
        Route::get('/messages', 'classMessages')->name('messages');
        Route::get('/{classSession}', 'show')->name('show');
    });

//student tracking
Route::delete('/student-tracker/{student_tracker}/unlink/{student}', [StudentTrackerController::class, 'unlink'])
    ->name('student-tracker.unlink');
Route::resource('student-tracker', StudentTrackerController::class)->only(['index', 'edit', 'update', 'destroy']);

//skill management
Route::prefix('skills')
    ->name('skills.')
    ->controller(\App\Http\Controllers\ClassManagement\SkillsController::class)
    ->group(function ()
    {
        Route::get('/', 'index')->name('index');
        Route::post('/store/knowledge', 'storeKnowledge')->name('store.knowledge');
        Route::post('/store/character', 'storeCharacter')->name('store.character');
        Route::get('/create/{category}/knowledge', 'createKnowledge')->name('create.knowledge');
        Route::get('/create/{category}/character', 'createCharacter')->name('create.character');
        Route::get('/knowledge/{skill}/rubric', 'knowledgeRubric')->name('rubric.knowledge');
        Route::get('/character/{skill}/rubric', 'characterRubric')->name('rubric.character');
        Route::get('/knowledge/{skill}/edit', 'editKnowledge')->name('edit.knowledge');
        Route::get('/character/{skill}/edit', 'editCharacter')->name('edit.character');
        Route::put('/knowledge/{skill}/update', 'updateKnowledge')->name('update.knowledge');
        Route::put('/character/{skill}/update', 'updateCharacter')->name('update.character');
        Route::delete('/knowledge/{skill}/unlink/{category}', 'unlinkKnowledgeCategory')->name('unlink.knowledge');
        Route::delete('/character/{skill}/unlink/{category}', 'unlinkCharacterCategory')->name('unlink.character');
        Route::post('/knowledge/{skill}/link', 'linkKnowledgeCategory')->name('link.knowledge');
        Route::post('/character/{skill}/link', 'linkCharacterCategory')->name('link.character');
        Route::get('/knowledge/{skill}', 'showKnowledge')->name('show.knowledge');
        Route::get('/character/{skill}', 'showCharacter')->name('show.character');
    });
