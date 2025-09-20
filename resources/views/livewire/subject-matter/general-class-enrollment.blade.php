<div>
    <div class="row">
        <div class="col-sm-4">
            <div class="card">
                <div class="card-header">
                    <div class="input-group mb-1">
                        <label for="campus_id"
                               class="input-group-text">{{ trans_choice('locations.campus', 1) }}</label>
                        <select id="campus_id" class="form-select" wire:model="campusId" wire:change="updateCampus()">
                            @foreach($campuses as $campus)
                                <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group mb-1">
                        <label for="year_id" class="input-group-text">{{ trans_choice('locations.years', 1) }}</label>
                        <select id="year_id" class="form-select" wire:model="yearId" wire:change="updateYear()">
                            @foreach($years as $year)
                                <option value="{{ $year->id }}">{{ $year->label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="my-2">
                        <span class="fw-bold me-2">{{ trans_choice('locations.terms',2) }}:</span>
                        @foreach($terms as $term)
                            <div class="form-check form-check-inline" wire:key="{{ $term->id }}">
                                <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="term-{{ $term->id }}"
                                        value="{{ $term->id }}"
                                        name="terms[]"
                                        wire:model="termIds"
                                        wire:click="updateCourse()"
                                >
                                <label class="form-check-label" for="term-{{ $term->id }}">{{ $term->label }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="input-group mb-1">
                        <label for="subject_id"
                               class="input-group-text">{{ trans_choice('subjects.subject', 1) }}</label>
                        <select id="subject_id" class="form-select" wire:model="subjectId"
                                wire:change="updateSubject()">
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group mb-1">
                        <label for="course_id" class="input-group-text">{{ trans_choice('subjects.course', 1) }}</label>
                        <select id="course_id" class="form-select" wire:model="courseId" wire:change="updateCourse()">
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body mh-100">
                    <div class="mb-3">
                        <div class="form-check">
                            <input
                                    class="form-check-input"
                                    type="radio"
                                    value="all"
                                    id="student-filter-all"
                                    wire:model="studentFilter"
                                    wire:click="refreshStudents()"
                            />
                            <label class="form-check-label"
                                   for="student-filter-all">{{ __('subjects.enrollment.general.students.all') }}</label>
                        </div>
                        <div class="form-check">
                            <input
                                    class="form-check-input"
                                    type="radio"
                                    value="enrolled"
                                    id="student-filter-enrolled"
                                    wire:model="studentFilter"
                                    wire:click="refreshStudents()"
                            />
                            <label class="form-check-label"
                                   for="student-filter-enrolled">{{ __('subjects.enrollment.general.students.enrolled') }}</label>
                        </div>
                        <div class="form-check">
                            <input
                                    class="form-check-input"
                                    type="radio"
                                    value="unenrolled"
                                    id="student-filter-unenrolled"
                                    wire:model="studentFilter"
                                    wire:click="refreshStudents()"
                            />
                            <label class="form-check-label"
                                   for="student-filter-unenrolled">{{ __('subjects.enrollment.general.students.unenrolled') }}</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8 text-center">
                            <label for="students" class="form-label">
                                {{ $students->count() }}
                                {{ trans_choice('people.student', $students->count()) }}
                            </label>
                            <select id="students" class="form-select" multiple style="height: 20vw;">
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->level->name }} &nbsp;
                                        {{ $student->person->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4 text-center">
                            <label for="levels"
                                   class="form-label">{{ trans_choice('crud.level',$levels->count()) }}</label>
                            <select id="levels" wire:model="levelIds" class="form-select" multiple
                                    wire:change="refreshStudents()">
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}"
                                            wire:key="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="row row-cols-2">
                @foreach($schoolClasses as $schoolClass)
                    <div class="col" wire:key="{{ $schoolClass->id }}">
                        <div class="card">
                            <div class="card-header">
                                <div class="row text-sm">
                                    <div class="col-5 border-end border-bottom">
                                        {{ $schoolClass->name }}
                                        <br/>
                                        {{ $schoolClass->sessions()->whereIn('term_id', $termIds)->get()->pluck('term.label')->join(', ') }}
                                    </div>
                                    <div class="col-7 text-end border-bottom">
                                        @foreach($schoolClass->sessions()->whereIn('term_id', $termIds)->get() as $session)
                                            {{ $session->locationString() }}
                                            ({{ $session->term->label }})
                                            @if(!$loop->last)
                                                <br/>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="row text-sm">
                                    <div class="col-5 border-end">
                                        @foreach($schoolClass->sessions()->whereIn('term_id', $termIds)->get() as $session)
                                            {{ $session->teachers->pluck('name')->join(', ') }}
                                            ({{ $session->term->label }})
                                            @if(!$loop->last)
                                                <br/>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="col-7 text-end">
                                        @foreach($schoolClass->sessions()->whereIn('term_id', $termIds)->get() as $session)
                                            {{ $session->scheduleString() }}
                                            ({{ $session->term->label }})
                                            @if(!$loop->last)
                                                <br/>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0" style="height: 400px;">
                                <select
                                        id="enrolled-students-{{ $schoolClass->id }}"
                                        class="form-select h-100 @if(isset($highlight) && $highlight == $schoolClass->id) glow-{{ $enroll? 'success': 'danger' }} @endif"
                                        multiple
                                >
                                    @foreach($schoolClass->students($this->termIds)->sortBy('person.last') as $student)
                                        <option
                                                value="{{ $student->id }}"
                                                @if(!$schoolClass->isEnrolled($student, $termIds))
                                                    class="bg-danger-subtle @if(isset($highlight) && $enroll && in_array($student->id, $studentIds)) glow-success @endif"
                                                @elseif(isset($highlight) && $enroll && in_array($student->id, $studentIds))
                                                    class="glow-success"
                                                @endif
                                        >
                                            {{ $student->level->name }} &nbsp;
                                            {{ $student->person->name }}
                                            @if(!$schoolClass->isEnrolled($student, $termIds))
                                                [{{ $student->classSessions->pluck('term.label')->join(', ') }}]
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <button
                                            type="button"
                                            class="btn btn-primary btn-sm col mx-2"
                                            wire:click="enrollStudents({{ $schoolClass->id }}, $('#students').val())"
                                    >{{ __('common.enroll') }}</button>
                                    <button
                                            type="button"
                                            class="btn btn-primary btn-sm col mx-2"
                                            wire:click="unenrollStudents({{ $schoolClass->id }}, $('#enrolled-students-{{ $schoolClass->id }}').val())"
                                    >{{ __('common.unenroll') }}</button>
                                </div>
                                <div class="text-end fs-5 fw-bold">
                                    {{ trans_choice('subjects.enrollment.general.students', $schoolClass->students($this->termIds)->count(), ['count' => $schoolClass->students($this->termIds)->count()]) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@script
<script>
    $wire.on('unhighlight-changes', () => {
        setTimeout(() => {
            $('.glow-danger').removeClass('glow-danger');
            $('.glow-success').removeClass('glow-success');
        }, 3000)
    });
</script>
@endscript
