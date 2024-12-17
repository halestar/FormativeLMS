<?php

namespace App\Http\Controllers\SubjectMatter;

use App\Http\Controllers\Controller;
use App\Models\Locations\Campus;
use App\Models\SubjectMatter\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SubjectsController extends Controller
{
    private static function errors(): array
    {
        return [
            'name' => __('errors.subjects.name'),
        ];
    }

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Campus $campus = null)
    {
        if(!$campus)
            $campus = \Auth::user()->employeeCampuses()->first();
        Gate::authorize('viewAny', Subject::class, $campus);
        $breadcrumb =
            [
                trans_choice('locations.campus', 2) => route('locations.campuses.index'),
                $campus->name => route('locations.campuses.show', ['campus' => $campus->id]),
                trans_choice('subjects.subject', 2) => '#'
            ];
        return view('subjects.subjects.index', compact('campus', 'breadcrumb'));
    }

    public function store(Campus $campus, Request $request)
    {
        Gate::authorize('store', Subject::class, $campus);
        $data = $request->validate(
            [
                'name' => 'required|max:255',
                'color' => 'required|hex_color',
            ], static::errors());
        $subject = new Subject();
        $data['order'] = Subject::count();
        $data['campus_id'] = $campus->id;
        $subject->fill($data);
        $subject->save();
        return redirect(route('subjects.subjects.edit', ['subject' => $subject->id]))
            ->with('success-status', __('subjects.subject.created'));
    }

    public function edit(Subject $subject)
    {
        Gate::authorize('update', $subject);
        $breadcrumb =
            [
                trans_choice('locations.campus', 2) => route('locations.campuses.index'),
                $subject->campus->name => route('locations.campuses.show', ['campus' => $subject->campus->id]),
                trans_choice('subjects.subject', 2) => route('subjects.subjects.index'),
                __('subjects.subject.edit') => '#'

            ];

        return view('subjects.subjects.edit', compact('subject', 'breadcrumb'));
    }

    public function update(Request $request, Subject $subject)
    {
        Gate::authorize('update', $subject);
        $data = $request->validate(
            [
                'name' => 'required|max:255',
                'color' => 'required|hex_color',
                'required_terms' => 'nullable|numeric|min:0',
            ], static::errors());
        $data['active'] = $request->input('active', 0);
        $subject->fill($data);
        $subject->save();
        return redirect(route('subjects.subjects.index', ['campus' => $subject->campus_id]))
            ->with('success-status', __('subjects.subject.updated'));
    }

    public function updateOrder(Request $request)
    {
        Gate::authorize('has-permission', 'subjects.subjects');
        $subjects = json_decode($request->input('subjects', "[]"));
        $idx = 1;
        foreach($subjects as $subjectId)
        {
            $subject = Subject::find($subjectId);
            if($subject)
            {
                $subject->order = $idx;
                $subject->save();
                $idx++;
            }
        }
        return redirect()->back()
            ->with('success-status', __('subjects.subject.updated'));
    }

    public function destroy(Subject $subject)
    {
        Gate::authorize('delete', $subject);
        $subject->delete();
        return redirect(route('subjects.subjects.index', ['campus' => $subject->campus->id]))
            ->with('success-status', __('subjects.subject.deleted'));
    }
}
