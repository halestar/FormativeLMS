@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="border-bottom d-flex justify-content-between align-items-baseline mb-3 pb-2" id="add-header">
            <div class="input-group w-50">
                <label for="campus_id" class="input-group-text">{{ __('subjects.course.viewing') }}</label>
                <select
                        class="form-select"
                        id="campus_id"
                        name="campus_id"
                        onchange="window.location.href = '/academics/courses/' + this.value"
                >
                    @foreach(Auth::user()->employeeCampuses as $campusOption)
                        <option value="{{ $campusOption->subjects()->first()->id }}"
                                @if($campusOption->id == $subject->campus_id) selected @endif>{{ $campusOption->name }}</option>
                    @endforeach
                </select>
                <select
                        id="subject_id"
                        class="form-select"
                        name="subject_id"
                        onchange="window.location.href = '/academics/courses/' + this.value"
                >
                    @foreach($subject->campus->subjects as $subjectOpts)
                        <option value="{{ $subjectOpts->id }}"
                                @if($subjectOpts->id == $subject->id) selected @endif>{{ $subjectOpts->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-check form-switch">
                <input
                        class="form-check-input"
                        type="checkbox"
                        role="switch"
                        id="show-inactive"
                        onclick="$('.course.inactive').toggleClass('d-none')"
                >
                <label class="form-check-label" for="show-inactive">{{ __('subjects.course.inactive.show') }}</label>
            </div>
            <button
                    class="btn btn-primary ms-3"
                    onclick="$('#add-container,#add-header').toggleClass('d-none')"
                    type="button"
            >
                <i class="fa fa-plus pe-1 me-1 border-end"></i>
                {{ __('subjects.course.add') }}
            </button>
        </div>
        <div class="card mb-2 text-bg-light d-none" id="add-container">
            <form action="{{ route('subjects.courses.store', ['subject' => $subject->id]) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <label for="name" class="form-label">{{ __('subjects.subject.name') }}</label>
                            <input
                                    type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    name="name"
                                    id="name"
                            />
                            <x-utilities.error-display key="name">{{ $errors->first('name') }}</x-utilities.error-display>
                        </div>
                        <div class="col-3">
                            <label for="subtitle" class="form-label">{{ __('subjects.course.subtitle') }}</label>
                            <input
                                    type="text"
                                    class="form-control"
                                    name="subtitle"
                                    id="subtitle"
                            />
                        </div>
                        <div class="col-2">
                            <label for="code" class="form-label">{{ __('subjects.course.code') }}</label>
                            <input
                                    type="text"
                                    class="form-control"
                                    name="code"
                                    id="code"
                            />
                        </div>
                        <div class="col-2">
                            <label for="credits" class="form-label">{{ __('subjects.course.credits') }}</label>
                            <input
                                    type="text"
                                    class="form-control"
                                    name="credits"
                                    id="credits"
                                    value="0"
                            />
                        </div>
                        <div class="col-2 align-self-end text-center">
                            <button type="submit" class="btn btn-primary">{{ __('subjects.course.add') }}</button>
                            <button
                                    type="button"
                                    onclick="$('#add-container,#add-header').toggleClass('d-none')"
                                    class="btn btn-secondary"
                            >{{ __('common.cancel') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="row fw-bold fs-6">
            <div class="col-sm-3">
                {{ __('subjects.course.name') }}
            </div>
            <div class="col-sm-1">
                {{ __('subjects.course.credits') }}
            </div>
            <div class="col-sm-1">
                {{ __('subjects.course.on_transcript') }}
            </div>
            <div class="col-sm-1">
                {{ __('subjects.course.gb_required') }}
            </div>
            <div class="col-sm-1">
                {{ __('subjects.course.honors') }}
            </div>
            <div class="col-sm-1">
                {{ __('subjects.course.ap') }}
            </div>
            <div class="col-sm-1">
                {{ __('subjects.course.can_assign_honors') }}
            </div>
            <div class="col-sm-1 text-center">
                {{ trans_choice('subjects.class', 2) }}
            </div>
        </div>
        <ul class="list-group course-list">
            @foreach($subject->courses as $course)
                <li
                        class="course list-group-item @if(!$course->active) inactive d-none opacity-50 @endif"
                        subject-id="{{ $course->id }}"
                >
                    <div class="row" course-id="{{ $course->id }}">
                        <span class="col-sm-3 align-self-center">{{ $course->course_name }}</span>
                        <span class="col-sm-1 align-self-center">{{ $course->credits }}</span>
                        <span class="col-sm-1 align-self-center">
                            @if($course->on_transcript)
                                <i class="fa-solid fa-check text-success"></i>
                            @else
                                <i class="fa-solid fa-times text-danger"></i>
                            @endif
                        </span>
                        <span class="col-sm-1 align-self-center">
                            @if($course->gb_required)
                                <i class="fa-solid fa-check text-success"></i>
                            @else
                                <i class="fa-solid fa-times text-danger"></i>
                            @endif
                        </span>
                        <span class="col-sm-1 align-self-center">
                            @if($course->honors)
                                <i class="fa-solid fa-check text-success"></i>
                            @else
                                <i class="fa-solid fa-times text-danger"></i>
                            @endif
                        </span>
                        <span class="col-sm-1 align-self-center">
                            @if($course->ap)
                                <i class="fa-solid fa-check text-success"></i>
                            @else
                                <i class="fa-solid fa-times text-danger"></i>
                            @endif
                        </span>
                        <span class="col-sm-1 align-self-center">
                            @if($course->can_assign_honors)
                                <i class="fa-solid fa-check text-success"></i>
                            @else
                                <i class="fa-solid fa-times text-danger"></i>
                            @endif
                        </span>
                        <span class="col-sm-1 align-self-center text-center">
                            <a href="{{ route('subjects.classes.index', ['course' => $course->id]) }}">
                                {{ $course->schoolClasses()->count() }}
                                {{ trans_choice('subjects.class',$course->schoolClasses()->count()) }}
                            </a>
                        </span>
                        <div class="col-sm-2 align-self-center text-end">
                            <a
                                    href="{{ route('subjects.courses.edit', ['course' => $course->id]) }}"
                                    class="btn btn-primary"
                            ><i class="fa-solid fa-edit"></i></a>
                            @can('delete', $course)
                                <button
                                        onclick="confirmDelete('{{ __('subjects.course.delete.confirm') }}', '{{ route('subjects.courses.destroy', ['course' => $course->id]) }}')"
                                        class="btn btn-danger"
                                ><i class="fa-solid fa-times"></i></button>
                            @endcan
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
