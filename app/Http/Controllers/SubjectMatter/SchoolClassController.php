<?php

namespace App\Http\Controllers\SubjectMatter;

use App\Classes\SessionSettings;
use App\Http\Controllers\Controller;
use App\Models\Locations\Term;
use App\Models\Locations\Year;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Course;
use App\Models\SubjectMatter\SchoolClass;
use App\Models\SubjectMatter\Subject;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class SchoolClassController extends Controller implements HasMiddleware
{
    private static function errors(): array
    {
        return [
            'terms' => __('errors.classes.terms'),
        ];
    }

    public function index(Request $request, ?Course $course = null)
    {
        if(!$course)
        {
            //first, do we have a saved subjects.course_id?
            if(SessionSettings::instance()->has('subjects.course_id'))
                $course = Course::find(SessionSettings::instance()->get('subjects.course_id'));
            elseif(SessionSettings::instance()->has('subjects.subject_id'))
                $course = Subject::find(SessionSettings::instance()->get('subjects.subject_id'))->courses()->first();
            else
                $course = SessionSettings::instance()->workingCampus()->subjects()->first()->courses()->first();
        }
        if($request->has('year_id'))
        {
            $year = Year::find($request->input('year_id'));
            SessionSettings::instance()->workingYear($year);
        }
        else
            $year = SessionSettings::instance()->workingYear();

        //save the course.
        SessionSettings::instance()->set('subjects.course_id', $course->id);
        //save the subject.
        SessionSettings::instance()->set('subjects.subject_id', $course->subject_id);
        //and save the campus
        SessionSettings::instance()->workingCampus($course->campus->campus_id);
        Gate::authorize('viewAny', SchoolClass::class);
        $breadcrumb =
            [
                $course->campus->name => route('locations.campuses.show', ['campus' => $course->campus->id]),
                $course->subject->name => route('subjects.subjects.index', ['campus' => $course->campus->id]),
                $course->name => route('subjects.courses.index', ['subject' => $course->subject_id]),
                trans_choice('subjects.class', 2) => '#'
            ];

        return view('subjects.classes.index', compact('course', 'breadcrumb', 'year'));
    }

    public function store(Course $course, Request $request)
    {
        Gate::authorize('store', SchoolClass::class);
        $data = $request->validate(
            [
                'terms' => 'required|array|min:1',
            ], static::errors());
        //first, we create the class
        $schoolClass = new SchoolClass();
        $schoolClass->course_id = $course->id;
        $schoolClass->year_id = SessionSettings::instance()->workingYear()->id;
        $schoolClass->save();
        //then we create the class sessions.
        foreach($data['terms'] as $term_id)
        {
            $term = Term::find($term_id);
            $classSession = new ClassSession();
            $classSession->class_id = $schoolClass->id;
            $classSession->term_id = $term->id;
            $classSession->save();
        }
        return redirect(route('subjects.classes.edit', ['schoolClass' => $schoolClass->id]))
            ->with('success-status', __('subjects.class.created'));
    }

    public function edit(SchoolClass $schoolClass)
    {
        Gate::authorize('update', $schoolClass);
        $breadcrumb =
            [
                $schoolClass->course->campus->name => route('locations.campuses.show', ['campus' => $schoolClass->course->campus->id]),
                $schoolClass->subject->name => route('subjects.subjects.index', ['campus' => $schoolClass->course->campus->id]),
                $schoolClass->course->name => route('subjects.courses.index', ['subject' => $schoolClass->course->subject_id]),
                trans_choice('subjects.class', 2) => route('subjects.classes.index', ['course' => $schoolClass->course_id]),
                __('subjects.class.edit') => '#',
            ];

        return view('subjects.classes.edit', compact('schoolClass', 'breadcrumb'));
    }

    public function destroy(SchoolClass $schoolClass)
    {
        Gate::authorize('delete', $schoolClass);
        $course_id = $schoolClass->course_id;
        $schoolClass->delete();
        return redirect(route('subjects.classes.index', ['course' => $course_id]))
            ->with('success-status', __('subjects.class.deleted'));
    }

	public static function middleware()
	{
		return ['auth'];
	}
}
