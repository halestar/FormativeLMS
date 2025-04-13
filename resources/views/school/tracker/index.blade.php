@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="border-bottom d-flex justify-content-between align-items-baseline mb-3 pb-2" id="add-header">
            <h3>{{ __('school.student.tracking') }}</h3>
        </div>
        <div class="row fw-bold fs-6">
            <div class="col-sm-3">
            </div>
        </div>
        <div class="list-group course-list">
            @foreach($trackers as $tracker)
                <a
                    href="{{ route('subjects.student-tracker.edit', ['student_tracker' => $tracker->id]) }}"
                    class="list-group-item list-group-item-action"
                >
                    <div class="row align-items-center">
                        <div class="h3 col-4">
                            <img
                                class="img-thumbnail img-fluid me-2"
                                src="{{ $tracker->thumbnail_url }}"
                                alt="{{ $tracker->name }}"
                                width="50"
                                height="50"
                            />
                            {{ $tracker->name }}
                        </div>
                        <div class="trackee-container col-7">
                            {{ __('school.student.tracking.current') }}
                            @if($tracker->studentTrackee()->count() > 0)
                                {{ $tracker->studentTrackee->pluck('person.name')->join(', ') }}
                            @else
                                {{ __('school.student.tracking.none') }}
                            @endif
                        </div>
                        <div class="campus-icon-container d-flex justify-content-end align-items-center col-1">
                            @foreach($tracker->employeeCampuses as $campus)
                                {!! $campus->iconHtml('normal') !!}
                            @endforeach
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
