@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container py-3 py-lg-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="text-uppercase small text-body-secondary fw-semibold mb-1">
                    {{ trans_choice('subjects.class', 2) }}
                </div>
                <h1 class="h3 mb-0">{{ $course->name }}</h1>
            </div>

            <button
                    class="btn btn-primary align-self-start align-self-lg-center"
                    onclick="$('#add-container').toggleClass('d-none')"
                    type="button"
            >
                <i class="fa fa-plus pe-1 me-1 border-end"></i>
                {{ __('subjects.class.add') }}
            </button>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-12 col-xl-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3 p-lg-4 d-flex flex-column gap-4">
                        <div>
                            <div class="text-uppercase small text-body-secondary fw-semibold mb-3">
                                {{ __('subjects.class.viewing', ['location' => ':']) }}
                            </div>

                            <div class="d-grid gap-3">
                                <div>
                                    <label for="campus_id" class="form-label fw-semibold mb-1">{{ trans_choice('locations.campus', 1) }}</label>
                                    <select
                                            class="form-select shadow-sm"
                                            id="campus_id"
                                            name="campus_id"
                                            onchange="window.location.href = '/academics/classes/' + this.value"
                                    >
                                        @foreach(Auth::user()->employeeCampuses as $campusOption)
                                            <option value="{{ $campusOption->subjects()->first()->courses()->first()->id }}"
                                                    @if($campusOption->id == $course->campus->id) selected @endif>{{ $campusOption->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="year_id" class="form-label fw-semibold mb-1">{{ trans_choice('locations.year', 1) }}</label>
                                    <select
                                            class="form-select shadow-sm"
                                            id="year_id"
                                            name="year_id"
                                            onchange="window.location.href = '/academics/classes/{{ $course->id }}?year_id=' + this.value"
                                    >
                                        @foreach(\App\Models\Locations\Year::all() as $yearOpts)
                                            <option value="{{ $yearOpts->id }}"
                                                    @if($year->id == $yearOpts->id) selected @endif>{{ $yearOpts->label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="subject_id" class="form-label fw-semibold mb-1">{{ trans_choice('subjects.subject', 1) }}</label>
                                    <select
                                            id="subject_id"
                                            class="form-select shadow-sm"
                                            name="subject_id"
                                            onchange="window.location.href = '/academics/classes/' + this.value"
                                    >
                                        @foreach($course->campus->subjects as $subjectOpts)
                                            <option value="{{ $subjectOpts->courses()->first()->id }}"
                                                    @if($subjectOpts->id == $course->subject_id) selected @endif>{{ $subjectOpts->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="course_id" class="form-label fw-semibold mb-1">{{ trans_choice('subjects.course', 1) }}</label>
                                    <select
                                            id="course_id"
                                            class="form-select shadow-sm"
                                            name="course_id"
                                            onchange="window.location.href = '/academics/classes/' + this.value"
                                    >
                                        @foreach($course->subject->courses as $courseOpts)
                                            <option value="{{ $courseOpts->id }}"
                                                    @if($courseOpts->id == $course->id) selected @endif>{{ $courseOpts->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded-3 bg-body-tertiary p-3">
                            <div class="fw-semibold mb-1">{{ $course->campus->name }}</div>
                            <div class="text-body-secondary small">{{ $year->label }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-8">
                <div class="card shadow-sm border-0 overflow-hidden mb-4 d-none" id="add-container">
                    <div class="card-body p-3 p-lg-4 bg-body-tertiary border-bottom">
                        <form action="{{ route('subjects.classes.store', ['course' => $course->id]) }}" method="POST">
                            @csrf
                            <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                                <div class="flex-grow-1">
                                    <div class="text-uppercase small text-body-secondary fw-semibold mb-2">
                                        {{ __('subjects.class.add') }}
                                    </div>
                                    <div class="fw-semibold fs-5 mb-3">Offer the class in the following terms:</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($year->campusTerms($course->campus)->get() as $term)
                                            <div class="form-check form-check-inline mb-0 border rounded-pill bg-white px-3 py-2 shadow-sm">
                                                <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        id="term-{{ $term->id }}"
                                                        value="{{ $term->id }}"
                                                        checked
                                                        name="terms[]"
                                                >
                                                <label class="form-check-label ms-1" for="term-{{ $term->id }}">{{ $term->label }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="d-flex flex-column flex-sm-row flex-lg-column justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary px-4">{{ __('subjects.class.add') }}</button>
                                    <button
                                            type="button"
                                            onclick="$('#add-container').toggleClass('d-none')"
                                            class="btn btn-outline-secondary px-4"
                                    >{{ __('common.cancel') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center px-3 px-lg-4 py-3 border-bottom">
                            <div>
                                <div class="text-uppercase small text-body-secondary fw-semibold mb-1">
                                    {{ trans_choice('subjects.class', 2) }}
                                </div>
                                <h2 class="h5 mb-0">{{ __('subjects.class.viewing', ['location' => $course->name]) }}</h2>
                            </div>
                            <span class="badge rounded-pill text-bg-primary px-3 py-2">{{ $course->schoolClasses($year)->get()->count() }}</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th class="ps-3 ps-lg-4">{{ __('subjects.class.name') }}</th>
                                    <th>{{ trans_choice('locations.terms', 1) }}</th>
                                    <th>{{ trans_choice('subjects.class.teacher', 2) }}</th>
                                    <th>{{ __('subjects.class.schedule') }}</th>
                                    <th>{{ trans_choice('locations.rooms', 1) }}</th>
                                    <th class="text-end pe-3 pe-lg-4">{{ __('common.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($course->schoolClasses($year)->get() as $schoolClass)
                                    @foreach($schoolClass->sessions as $session)
                                        <tr>
                                            @if($loop->first)
                                                <td class="ps-3 ps-lg-4 align-middle" rowspan="{{ $schoolClass->sessions->count() }}">
                                                    <div class="fw-semibold fs-5">{{ $schoolClass->name }}</div>
                                                    <div class="small text-body-secondary mt-1">{{ $schoolClass->sessions->count() }} {{ trans_choice('locations.terms', $schoolClass->sessions->count()) }}</div>
                                                </td>
                                            @endif
                                            <td>
                                                <span class="badge rounded-pill text-bg-light border text-body-secondary px-3 py-2">{{ $session->term->label }}</span>
                                            </td>
                                            <td>
                                                @if($session->teachers->isNotEmpty())
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach($session->teachers as $teacher)
                                                            <a
                                                                    href="{{ route('people.show', $teacher->school_id) }}"
                                                                    class="text-decoration-none badge rounded-pill text-bg-primary-subtle border text-primary-emphasis px-3 py-2"
                                                            >{{ $teacher->name }}</a>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-body-secondary">{{ __('common.results.no.found') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $session->scheduleString() }}</div>
                                            </td>
                                            <td>{!! $session->locationString(true) !!}</td>
                                            @if($loop->first)
                                                <td class="align-middle text-end pe-3 pe-lg-4" rowspan="{{ $schoolClass->sessions->count() }}">
                                                    <div class="d-inline-flex flex-column align-items-end gap-2">
                                                        <a
                                                                href="{{ route('subjects.classes.edit', ['schoolClass' => $schoolClass->id]) }}"
                                                                class="btn btn-primary btn-sm rounded-pill px-3"
                                                        >
                                                            <i class="fa-solid fa-edit me-1"></i>{{ __('common.edit') }}
                                                        </a>
                                                        @can('delete', $schoolClass)
                                                            <button
                                                                    onclick="confirmDelete('{{ __('subjects.class.delete.confirm') }}', '{{ route('subjects.classes.destroy', ['schoolClass' => $schoolClass->id]) }}')"
                                                                    class="btn btn-outline-danger btn-sm rounded-pill px-3 text-nowrap"
                                                            >
                                                                <i class="fa-solid fa-times me-1"></i>{{ __('common.delete') }}
                                                            </button>
                                                        @endcanany
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-body-secondary">
                                            {{ __('common.results.no.found') }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
