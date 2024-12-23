@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="border-bottom d-flex justify-content-between align-items-baseline mb-3 pb-2" id="add-header">
            <div class="input-group w-75">
                <label for="campus_id" class="input-group-text">{{ __('subjects.class.viewing') }}</label>
                <select
                    class="form-select"
                    id="campus_id"
                    name="campus_id"
                    onchange="window.location.href = '/academics/classes/' + this.value"
                >
                    @foreach(Auth::user()->employeeCampuses as $campusOption)
                        <option value="{{ $campusOption->subjects()->first()->courses()->first()->id }}" @if($campusOption->id == $course->campus->id) selected @endif>{{ $campusOption->name }}</option>
                    @endforeach
                </select>
                <select
                    class="form-select"
                    id="year_id"
                    name="year_id"
                    onchange="window.location.href = '/academics/classes/{{ $course->id }}?year_id=' + this.value"
                >
                    @foreach(\App\Models\Locations\Year::all() as $yearOpts)
                        <option value="{{ $yearOpts->id }}" @if($year->id == $yearOpts->id) selected @endif>{{ $yearOpts->label }}</option>
                    @endforeach
                </select>
                <select
                    id="subject_id"
                    class="form-select"
                    name="subject_id"
                    onchange="window.location.href = '/academics/classes/' + this.value"
                >
                    @foreach($course->campus->subjects as $subjectOpts)
                        <option value="{{ $subjectOpts->courses()->first()->id }}" @if($subjectOpts->id == $course->subject_id) selected @endif>{{ $subjectOpts->name }}</option>
                    @endforeach
                </select>
                <select
                    id="course_id"
                    class="form-select"
                    name="course_id"
                    onchange="window.location.href = '/academics/classes/' + this.value"
                >
                    @foreach($course->subject->courses as $courseOpts)
                        <option value="{{ $courseOpts->id }}" @if($courseOpts->id == $course->id) selected @endif>{{ $courseOpts->name }}</option>
                    @endforeach
                </select>
            </div>

            <button
                class="btn btn-primary ms-3"
                onclick="$('#add-container,#add-header').toggleClass('d-none')"
                type="button"
            >
                <i class="fa fa-plus pe-1 me-1 border-end"></i>
                {{ __('subjects.class.add') }}
            </button>
        </div>
        <div class="card mb-2 text-bg-light d-none" id="add-container">
            <form action="{{ route('subjects.classes.store', ['course' => $course->id]) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-10 align-self-center">
                            <span class="fs-6 fw-bold me-3">Offer the class in the following terms:</span>
                            @foreach($year->campusTerms($course->campus)->get() as $term)
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="term-{{ $term->id }}"
                                        value="{{ $term->id }}"
                                        checked
                                        name="terms[]"
                                    >
                                    <label class="form-check-label" for="term-{{ $term->id }}">{{ $term->label }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-2 align-self-end text-center">
                            <button type="submit" class="btn btn-primary">{{ __('subjects.class.add') }}</button>
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
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('subjects.class.name') }}</th>
                    <th></th>
                    <th>{{ __('subjects.class.teacher') }}</th>
                    <th>{{ __('subjects.class.schedule') }}</th>
                    <th>{{ __('subjects.class.location') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($course->schoolClasses($year)->get() as $schoolClass)
                @foreach($schoolClass->sessions as $session)
                <tr>
                    @if($loop->first)
                    <td class="fs-5 align-middle" rowspan="{{ $schoolClass->sessions->count() }}">
                        {{ $schoolClass->name }}
                    </td>
                    @endif
                    <td>{{ $session->term->label }}</td>
                    <td>{{ $session->teachers->pluck('name')->join(', ') }}</td>
                    <td>{{ $session->scheduleString() }}</td>
                    <td>{{ $session->locationString() }}</td>
                    @if($loop->first)
                    <td class="align-middle text-end" rowspan="{{ $schoolClass->sessions->count() }}">
                        <a
                            href="{{ route('subjects.classes.edit', ['schoolClass' => $schoolClass->id]) }}"
                            class="btn btn-primary"
                        ><i class="fa-solid fa-edit"></i></a>
                        @can('delete', $schoolClass)
                        <button
                            onclick="confirmDelete('{{ __('subjects.class.delete.confirm') }}', '{{ route('subjects.classes.destroy', ['schoolClass' => $schoolClass->id]) }}')"
                            class="btn btn-danger"
                        ><i class="fa-solid fa-times"></i></button>
                        @endcanany
                    </td>
                    @endif
                </tr>
            @endforeach
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
