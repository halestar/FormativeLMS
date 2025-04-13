<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Http\Request;


class StudentTrackerController extends Controller
{
    private static function errors(): array
    {
        return [
            'person_id' => __('errors.school.student-tracking.person_id'),
            'student' => __('errors.school.student-tracking.student'),
        ];
    }

    public function __construct()
    {
        $this->middleware(
            [
                'auth',
                'permission:school.tracker.admin'
            ]);
    }
    public function index()
    {
        $breadcrumb =
            [
                __('school.student.tracking') => '#',
            ];
        $self = auth()->user();
        $trackers = Person::permission('school.tracker')
            ->select('people.*')
            ->join('employee_campuses', 'people.id', '=', 'employee_campuses.person_id')
            ->whereIn('campus_id', $self->employeeCampuses->pluck('id'))
            ->groupBy('people.id')
            ->get();
        return view('school.tracker.index', compact('breadcrumb', 'trackers'));
    }

    public function edit(Person $studentTracker)
    {
        $breadcrumb =
            [
                __('school.student.tracking') => route('subjects.student-tracker.index'),
                $studentTracker->name => '#',
            ];
        $filterRoles = collect([SchoolRoles::$STUDENT]);
        return view('school.tracker.edit', compact('breadcrumb', 'studentTracker', 'filterRoles'));
    }

    public function update(Request $request, Person $studentTracker)
    {
        $data = $request->validate([
            'person_id' => 'required|exists:people,id',
        ], static::errors());
        $student = Person::find($data['person_id'])->student();
        if($student)
        {
            $studentTracker->studentTrackee()->attach($student->id);
            $studentTracker->save();
        }
        return redirect()->route('subjects.student-tracker.edit', $studentTracker);
    }

    public function unlink(Request $request, Person $studentTracker, StudentRecord $student)
    {
        $studentTracker->studentTrackee()->detach($student->id);
        return redirect()->route('subjects.student-tracker.edit', $studentTracker);
    }
}
