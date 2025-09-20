@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <h3 class="border-bottom mb-3 d-flex justify-content-between align-items-center">
            <span>{{ __('school.student.tracking.student') }} {{ $studentTracker->name }}</span>
            <button class="btn btn-primary" type="button"
                    onclick="$('#assign-student-container').removeClass('d-none')">
                {{ __('school.student.tracking.link') }}
            </button>
        </h3>
        <form action="{{ route('subjects.student-tracker.update', $studentTracker) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="alert alert-dark d-none" id="assign-student-container">
                <label for="person-selector-search"
                       class="form-label">{{{ __('school.student.tracking.select.student') }}}</label>
                <livewire:people.person-selector
                        :filter-roles="$filterRoles"
                        selectedCB="selectPerson"
                        clearedCB="clearPerson"
                />
                <input type="hidden" id="person_id" name="person_id" value=""/>
                <div class="text-end mt-2">
                    <button type="submit" class="btn btn-primary mx-2">Add Student</button>
                    <button type="button" class="btn btn-secondary"
                            onclick="$('#assign-student-container').addClass('d-none')">Cancel
                    </button>
                </div>
            </div>
        </form>
        <div class="d-flex justify-content-start flex-wrap">
            @foreach($studentTracker->studentTrackee as $trackee)
                <div class="card m-2">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">{{ $trackee->person->name }}</h5>
                        <button
                                class="btn btn-danger btn-sm ms-5"
                                type="button"
                                onclick='confirmDelete("{{__('school.student.tracking.delete.confirm')}}", "{{ route('subjects.student-tracker.unlink', ['student_tracker' => $studentTracker->id, 'student' => $trackee->id]) }}" )'
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function selectPerson(person) {
            $('#person_id').val(person.id);
        }

        function clearPerson() {
            $('#person_id').val('');
        }
    </script>
@endpush
