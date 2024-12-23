<?php

namespace App\Http\Controllers\SubjectMatter;

use App\Classes\SessionSettings;
use App\Http\Controllers\Controller;
use App\Models\SubjectMatter\Course;
use App\Models\SubjectMatter\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    private static function errors(): array
    {
        return [
            'name' => __('errors.courses.name'),
        ];
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(?Subject $subject = null)
    {
        //get the first subject from the working campus
        if(!$subject)
        {
            //first, do we have a saved subjects.subject_id?
            if(SessionSettings::instance()->has('subjects.subject_id'))
                $subject = Subject::find(SessionSettings::instance()->get('subjects.subject_id'));
            else
                $subject = SessionSettings::instance()->workingCampus()->subjects()->first();
        }
        //save the subject.
        SessionSettings::instance()->set('subjects.subject_id', $subject->id);
        //and save the campus
        SessionSettings::instance()->workingCampus($subject->campus_id);
        Gate::authorize('viewAny', Course::class);
        $breadcrumb =
            [
                $subject->campus->name => route('locations.campuses.show', ['campus' => $subject->campus_id]),
                $subject->name => route('subjects.subjects.index', ['campus' => $subject->campus_id]),
                trans_choice('subjects.course', 2) => '#'
            ];
        return view('subjects.courses.index', compact('subject', 'breadcrumb'));
    }

    public function store(Subject $subject, Request $request)
    {
        Gate::authorize('store', Course::class);
        $data = $request->validate(
            [
                'name' => 'required|max:255',
                'subtitle' => 'nullable|max:255',
                'code' => 'nullable|max:255',
                'credits' => 'required|numeric|min:0',
            ], static::errors());
        $course = new Course();
        $course->fill($data);
        $course->subject_id = $subject->id;
        $course->save();
        return redirect(route('subjects.courses.edit', ['course' => $course->id]))
            ->with('success-status', __('subjects.course.created'));
    }

    public function edit(Course $course)
    {
        Gate::authorize('update', $course);
        $breadcrumb =
            [
                trans_choice('locations.campus', 2) => route('locations.campuses.index'),
                $course->campus->name => route('locations.campuses.show', ['campus' => $course->campus->id]),
                trans_choice('subjects.subject', 2) => route('subjects.subjects.index'),
                $course->subject->name => route('subjects.subjects.index', ['campus' => $course->campus->id]),
                trans_choice('subjects.course', 2) => route('subjects.courses.index', ['subject' => $course->subject_id]),
                __('subjects.course.edit') => '#',

            ];
        return view('subjects.courses.edit', compact('course', 'breadcrumb'));
    }

    public function update(Request $request, Course $course)
    {
        Gate::authorize('update', $course);
        $data = $request->validate(
            [
                'name' => 'required|max:255',
                'subtitle' => 'nullable|max:255',
                'code' => 'nullable|max:255',
                'description' => 'nullable|max:255',
                'credits' => 'required|numeric|min:0',
            ], static::errors());
        $data['on_transcript'] = $request->input('on_transcript', 0);
        $data['gb_required'] = $request->input('gb_required', 0);
        $data['honors'] = $request->input('honors', 0);
        $data['ap'] = $request->input('ap', 0);
        $data['can_assign_honors'] = $request->input('can_assign_honors', 0);
        $data['active'] = $request->input('active', 0);
        $course->fill($data);
        $course->save();
        return redirect(route('subjects.courses.index', ['subject' => $course->subject_id]))
            ->with('success-status', __('subjects.course.updated'));
    }

    public function destroy(Course $course)
    {
        Gate::authorize('delete', $course);
        $subject_id = $course->subject_id;
        $course->delete();
        return redirect(route('subjects.courses.index', ['subject' => $subject_id]))
            ->with('success-status', __('subjects.course.deleted'));
    }
}
