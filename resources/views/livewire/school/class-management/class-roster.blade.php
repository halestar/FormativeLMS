<div class="w-100">
    <h4>{{ trans_choice('subjects.class.teacher', $teachers->count()) }}</h4>
    <div class="d-flex justify-content-start align-content-start">
        @foreach($teachers as $teacher)
            <div class="card mx-1">
                <img src="{{ $teacher->portrait_url }}" alt="{{ $teacher->name }}" class="card-img-top" />
                <div class="card-body">
                    <a
                        class="fw-bold link-dark link-underline-opacity-0 link-underline-opacity-100-hover text-wrap fs-5"
                        href="{{ route('people.show', $teacher->school_id) }}"
                    >{{ $teacher->name }}</a>
                </div>
            </div>
        @endforeach
    </div>
    <h4>{{ trans_choice('people.student', $students->count()) }}</h4>
    <div class="d-flex flex-wrap">
        @foreach($students as $student)
            <div class="card mx-1 col">
                <img
                    src="{{ $student->person->portrait_url }}"
                    alt="{{ $student->person->name }}"
                    class="card-img-top"
                />
                <div class="card-body text-center align-content-center">
                    <a
                        class="fw-bold link-dark link-underline-opacity-0 link-underline-opacity-100-hover text-wrap fs-5"
                        href="{{ route('people.show', $student->person->school_id) }}"
                    >{{ $student->person->name }}</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
