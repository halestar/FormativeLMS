@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
<div class="container">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button
                class="nav-link active"
                id="school-tab"
                data-bs-toggle="tab"
                data-bs-target="#school-tabpane"
                type="button"
                role="tab"
                aria-controls="school-tabpane"
                aria-selected="true"
            >{{ __('system.settings.tabs.school') }}</button >
        </li>
        <li class="nav-item" role="presentation">
            <button
                class="nav-link"
                id="classes-tab"
                data-bs-toggle="tab"
                data-bs-target="#classes-tabpane"
                type="button"
                role="tab"
                aria-controls="classes-tabpane"
                aria-selected="false"
            >{{ __('system.settings.tabs.classes') }}</button >
        </li>
    </ul>
    <div class="tab-content mt-2">
        <div class="tab-pane fade show active" id="school-tabpane" role="tabpanel" aria-labelledby="school-tab" tabindex="0">
            <form action="{{ route('school.settings.update.school') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="school-setting mb-3">
                    <label class="form-label">{{ __('system.settings.days') }}</label>
                    <br/>
                    @foreach(\App\Classes\Days::allOptions() as $dayId => $day)
                        <div class="form-check-inline">
                            <input
                                class="form-check-input @error('days') is-invalid @enderror"
                                type="checkbox"
                                value="{{ $dayId }}"
                                name="days[]"
                                id="day_{{ $dayId }}"
                                @checked(isset($schoolSettings->days[$dayId]))
                                aria-describedby="daysHelp"
                            />
                            <label class="form-check-label" for="day_{{ $dayId }}">{{ $day }}</label>
                        </div>
                    @endforeach
                    <x-error-display key="days">{{ $errors->first('days') }}</x-error-display>
                    <div id="daysHelp" class="form-text">{{ __('system.settings.days.help') }}</div>
                </div>
                <div class="school-setting mb-3">
                    <label for="start_time">{{ __('system.settings.start') }}</label>
                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time"
                           value="{{ $schoolSettings->startTime }}" aria-describedby="startTimeHelp"/>
                    <x-error-display key="start_time">{{ $errors->first('start_time') }}</x-error-display>
                    <div id="startTimeHelp" class="form-text">{{ __('system.settings.start.help') }}</div>
                </div>
                <div class="school-setting mb-3">
                    <label for="end_time">{{ __('system.settings.end') }}</label>
                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time"
                           value="{{ $schoolSettings->endTime }}" aria-describedby="endTimeHelp"/>
                    <x-error-display key="end_time">{{ $errors->first('end_time') }}</x-error-display>
                    <div id="endTimeHelp" class="form-text">{{ __('system.settings.end.help') }}</div>
                </div>
                <div class="school-setting mb-3">
                    <label for="student_name_format">{{ __('system.settings.names.student') }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control"
                               value="{{ $schoolSettings->studentName->__toString() }}" aria-describedby="studentNameHelp" readonly />
                        <a class="btn btn-primary" href="{{ route('school.settings.name', $studentRole) }}" role="button"><i class="fa-solid fa-edit"></i></a>
                    </div>
                    <div id="studentNameHelp" class="form-text">{{ __('system.settings.names.students.help', ['sample' => $sampleStudent->name]) }}</div>
                </div>
                <div class="school-setting mb-3">
                    <label for="employee_name_format">{{ __('system.settings.names.employee') }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control"
                               value="{{ $schoolSettings->employeeName->__toString() }}" aria-describedby="employeeNameHelp" readonly />
                        <a class="btn btn-primary" href="{{ route('school.settings.name', $employeeRole) }}" role="button"><i class="fa-solid fa-edit"></i></a>
                    </div>
                    <div id="employeeNameHelp" class="form-text">{{ __('system.settings.names.employee.help', ['sample' => $sampleEmployee->name]) }}</div>
                </div>
                <div class="school-setting mb-3">
                    <label for="parent_name_format">{{ __('system.settings.names.parent') }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control"
                               value="{{ $schoolSettings->parentName->__toString() }}" aria-describedby="parentNameHelp" readonly />
                        <a class="btn btn-primary" href="{{ route('school.settings.name', $parentRole) }}" role="button"><i class="fa-solid fa-edit"></i></a>
                    </div>
                    <div id="parentNameHelp" class="form-text">{{ __('system.settings.names.parent.help', ['sample' => $sampleParent->name]) }}</div>
                </div>
                <div class="row">
                    <button type="submit" class="btn btn-primary col">{{ __('system.settings.update') }}</button>
                </div>
            </form>
        </div>
        <div class="tab-pane fade" id="classes-tabpane" role="tabpanel" aria-labelledby="classes-tab" tabindex="1">
            <form action="{{ route('school.settings.update.classes') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="school-setting mb-3">
                    <label for="max_msg">{{ __('system.settings.classes.max_msg') }}</label>
                    <input
                        type="number"
                        class="form-control @error('max_msg') is-invalid @enderror"
                        id="max_msg"
                        name="max_msg"
                        value="{{ $schoolSettings->max_msg }}"
                        aria-describedby="maxMsgHelp"/>
                    <x-error-display key="max_msg">{{ $errors->first('max_msg') }}</x-error-display>
                    <div id="maxMsgHelp" class="form-text">{{ __('system.settings.classes.max_msg.help') }}</div>
                </div>

                <div class="school-setting mb-3">
                    <label class="form-label">{{ __('system.settings.classes.year_messages') }}</label>
                    <br/>
                    <div class="form-check-inline">
                        <input
                            class="form-check-input @error('days') is-invalid @enderror"
                            type="radio"
                            value="{{ \App\Classes\SchoolSettings::TERM }}"
                            name="year_messages"
                            id="year_messages_{{ \App\Classes\SchoolSettings::TERM }}"
                            @checked($schoolSettings->year_messages == \App\Classes\SchoolSettings::TERM)
                            aria-describedby="yearMessagesHelp"
                        />
                        <label class="form-check-label" for="year_messages_{{ \App\Classes\SchoolSettings::TERM }}">
                            {{ __('system.settings.classes.year_messages.term') }}
                        </label>
                    </div>
                    <div class="form-check-inline">
                        <input
                            class="form-check-input @error('days') is-invalid @enderror"
                            type="radio"
                            value="{{ \App\Classes\SchoolSettings::YEAR }}"
                            name="year_messages"
                            id="year_messages_{{ \App\Classes\SchoolSettings::YEAR }}"
                            @checked($schoolSettings->year_messages == \App\Classes\SchoolSettings::YEAR)
                            aria-describedby="yearMessagesHelp"
                        />
                        <label class="form-check-label" for="year_messages_{{ \App\Classes\SchoolSettings::YEAR }}">
                            {{ __('system.settings.classes.year_messages.year') }}
                        </label>
                    </div>
                    <x-error-display key="days">{{ $errors->first('days') }}</x-error-display>
                    <div id="daysHelp" class="form-text">{{ __('system.settings.classes.year_messages.help') }}</div>
                </div>

                <div class="row">
                    <button type="submit" class="btn btn-primary col">{{ __('system.settings.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
