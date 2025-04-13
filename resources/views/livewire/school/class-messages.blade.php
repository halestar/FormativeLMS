<div class="container">
    @if($multipleRoles)
        <div class="row">
            <div class="col-lg-3">
                {{ __('subjects.school.message.view.as') }}
            </div>
            @if($isFaculty)
                <div class="col-lg-3 form-check form-check-inline">
                    <input
                        type="radio"
                        wire:model="viewAs"
                        value="faculty"
                        id="view_as_faculty"
                    />
                    <label for="view_as_faculty">{{ __('common.faculty') }}</label>
                </div>
            @elseif($isStudent)
                <div class="col-lg-3 form-check form-check-inline">
                    <input
                        type="radio"
                        wire:model="viewAs"
                        value="student"
                        id="view_as_student"
                    />
                    <label for="view_as_student">{{ __('common.student') }}</label>
                </div>
            @elseif($isParent)
                <div class="col-lg-3 form-check form-check-inline">
                    <input
                        type="radio"
                        wire:model="viewAs"
                        value="parent"
                        id="view_as_parent"
                    />
                    <label for="view_as_parent">{{ __('common.parent') }}</label>
                </div>
            @endif
        </div>
    @endif
    <div class="row">
        <div class="col-lg-12">
            <div class="input-group">
                <label class="input-group-text" for="year-select">{{ __('subjects.school.message.view.messages') }}</label>
                <select wire:model="selectedYearId" class="form-select" id="year-select" wire:change="setYear()">
                    @foreach($years as $year)
                        <option value="{{ $year->id }}">{{ $year->label }}</option>
                    @endforeach
                </select>
                @if($viewAs == "parent")
                <select wire:model="selectedStudentId" class="form-select" id="student-select" wire:change="setStudent()">
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->person->name }}</option>
                    @endforeach
                </select>
                @endif
                <select wire:model="selectedTermId" class="form-select" model:key="{{ $selectedYearId }}" wire:change="setTerm()">
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" @selected($term->id == $selectedTerm->id)>{{ $term->label }} ({{$term->campus->name}})</option>
                    @endforeach
                </select>
                @if($viewAs == "faculty")
                <select wire:model="selectedSessionId" class="form-select" wire:change="setSession()">
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                    @endforeach
                </select>
                @endif
            </div>
        </div>
    </div>
    <div class="row mt-3">
        @if($viewAs == "faculty")
            @if($selectedSession)
                <livewire:school.faculty-class-chat :session="$selectedSession" :selected-student-id="$selectedStudentId" size="lg" />
            @endif
        @elseif($viewAs == "student")
            @if($selectedTerm)
                <livewire:school.student-class-chat :term="$selectedTerm" :selected-session-id="$selectedSessionId" size="lg" />
            @endif
        @elseif($viewAs == "parent")
            @if($selectedTerm && $selectedStudent)
                <livewire:school.parent-class-chat :student="$selectedStudent" :selected-session-id="$selectedSessionId" :term="$selectedTerm" size="lg" />
            @endif
        @endif
    </div>
</div>
