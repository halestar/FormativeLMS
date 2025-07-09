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
                >{{ __('system.settings.tabs.school') }}</button>
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
                >{{ __('system.settings.tabs.classes') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link"
                    id="id-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#id-tabpane"
                    type="button"
                    role="tab"
                    aria-controls="id-tabpane"
                    aria-selected="false"
                >{{ trans_choice('people.id', 2) }}</button>
            </li>
        </ul>
        <div class="tab-content mt-2">
            <div class="tab-pane fade show active" id="school-tabpane" role="tabpanel" aria-labelledby="school-tab"
                 tabindex="0">
                <form action="{{ route('settings.school.update.school') }}" method="POST">
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
                        <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                               id="start_time" name="start_time"
                               value="{{ $schoolSettings->startTime }}" aria-describedby="startTimeHelp"/>
                        <x-error-display key="start_time">{{ $errors->first('start_time') }}</x-error-display>
                        <div id="startTimeHelp" class="form-text">{{ __('system.settings.start.help') }}</div>
                    </div>
                    <div class="school-setting mb-3">
                        <label for="end_time">{{ __('system.settings.end') }}</label>
                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time"
                               name="end_time"
                               value="{{ $schoolSettings->endTime }}" aria-describedby="endTimeHelp"/>
                        <x-error-display key="end_time">{{ $errors->first('end_time') }}</x-error-display>
                        <div id="endTimeHelp" class="form-text">{{ __('system.settings.end.help') }}</div>
                    </div>
                    <div class="school-setting mb-3">
                        <label for="student_name_format">{{ __('system.settings.names.student') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   value="{{ $schoolSettings->studentName->__toString() }}"
                                   aria-describedby="studentNameHelp" readonly/>
                            <a class="btn btn-primary" href="{{ route('settings.school.name', $studentRole) }}"
                               role="button"><i class="fa-solid fa-edit"></i></a>
                        </div>
                        <div id="studentNameHelp"
                             class="form-text">{{ __('system.settings.names.students.help', ['sample' => $sampleStudent->name]) }}</div>
                    </div>
                    <div class="school-setting mb-3">
                        <label for="employee_name_format">{{ __('system.settings.names.employee') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   value="{{ $schoolSettings->employeeName->__toString() }}"
                                   aria-describedby="employeeNameHelp" readonly/>
                            <a class="btn btn-primary" href="{{ route('settings.school.name', $employeeRole) }}"
                               role="button"><i class="fa-solid fa-edit"></i></a>
                        </div>
                        <div id="employeeNameHelp"
                             class="form-text">{{ __('system.settings.names.employee.help', ['sample' => $sampleEmployee->name]) }}</div>
                    </div>
                    <div class="school-setting mb-3">
                        <label for="parent_name_format">{{ __('system.settings.names.parent') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   value="{{ $schoolSettings->parentName->__toString() }}"
                                   aria-describedby="parentNameHelp" readonly/>
                            <a class="btn btn-primary" href="{{ route('settings.school.name', $parentRole) }}"
                               role="button"><i class="fa-solid fa-edit"></i></a>
                        </div>
                        <div id="parentNameHelp"
                             class="form-text">{{ __('system.settings.names.parent.help', ['sample' => $sampleParent->name]) }}</div>
                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-primary col">{{ __('system.settings.update') }}</button>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="classes-tabpane" role="tabpanel" aria-labelledby="classes-tab" tabindex="1">
                <form action="{{ route('settings.school.update.classes') }}" method="POST">
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
                                value="{{ \App\Classes\Settings\SchoolSettings::TERM }}"
                                name="year_messages"
                                id="year_messages_{{ \App\Classes\Settings\SchoolSettings::TERM }}"
                                @checked($schoolSettings->year_messages == \App\Classes\Settings\SchoolSettings::TERM)
                                aria-describedby="yearMessagesHelp"
                            />
                            <label class="form-check-label"
                                   for="year_messages_{{ \App\Classes\Settings\SchoolSettings::TERM }}">
                                {{ __('system.settings.classes.year_messages.term') }}
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <input
                                class="form-check-input @error('days') is-invalid @enderror"
                                type="radio"
                                value="{{ \App\Classes\Settings\SchoolSettings::YEAR }}"
                                name="year_messages"
                                id="year_messages_{{ \App\Classes\Settings\SchoolSettings::YEAR }}"
                                @checked($schoolSettings->year_messages == \App\Classes\Settings\SchoolSettings::YEAR)
                                aria-describedby="yearMessagesHelp"
                            />
                            <label class="form-check-label"
                                   for="year_messages_{{ \App\Classes\Settings\SchoolSettings::YEAR }}">
                                {{ __('system.settings.classes.year_messages.year') }}
                            </label>
                        </div>
                        <x-error-display key="days">{{ $errors->first('days') }}</x-error-display>
                        <div id="daysHelp"
                             class="form-text">{{ __('system.settings.classes.year_messages.help') }}</div>
                    </div>

                    <div class="row">
                        <button type="submit" class="btn btn-primary col">{{ __('system.settings.update') }}</button>
                    </div>
                </form>
            </div>
            <div class="tab-content mt-2">
                <div class="tab-pane fade " id="id-tabpane" role="tabpanel" aria-labelledby="id-tab"
                     tabindex="0">
                    <div class="row">
                        <div class="col">
                            <form action="{{ route('settings.school.update.ids') }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <h3>School ID Strategy</h3>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="id_strategy"
                                        id="id_strategy_global"
                                        value="{{ \App\Classes\Settings\IdSettings::ID_STRATEGY_GLOBAL }}"
                                        @checked($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_GLOBAL)
                                    />
                                    <label class="form-check-label" for="id_strategy_global">
                                        {{ trans_choice('people.id.global', 1) }}
                                    </label>
                                </div>
                                <div class="alert alert-info">
                                    {{ __('people.id.global.help') }}
                                </div>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="id_strategy"
                                        id="id_strategy_roles"
                                        value="{{ \App\Classes\Settings\IdSettings::ID_STRATEGY_ROLES }}"
                                        @checked($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_ROLES)
                                    >
                                    <label class="form-check-label" for="id_strategy_roles">
                                        {{ __('people.id.roles') }}
                                    </label>
                                </div>
                                <div class="alert alert-info">
                                    {{ __('people.id.roles.help') }}
                                </div>

                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="id_strategy"
                                        id="id_strategy_campuses"
                                        value="{{ \App\Classes\Settings\IdSettings::ID_STRATEGY_CAMPUSES }}"
                                        @checked($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_CAMPUSES)
                                    />
                                    <label class="form-check-label" for="id_strategy_campuses">
                                        {{ __('people.id.campuses') }}
                                    </label>
                                </div>
                                <div class="alert alert-info">
                                    {{ __('people.id.campuses.help') }}
                                </div>

                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="id_strategy"
                                        id="id_strategy_both"
                                        value="{{ \App\Classes\Settings\IdSettings::ID_STRATEGY_BOTH }}"
                                        @checked($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_BOTH)
                                    />
                                    <label class="form-check-label" for="id_strategy_both">
                                        {{ __('people.id.both') }}
                                    </label>
                                </div>
                                <div class="alert alert-info">
                                    {{ __('people.id.both.help') }}
                                </div>
                                <div class="row">
                                    <button type="submit" class="btn btn-primary col">{{ __('common.update') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="col">
                            <h3 class="my-3">{{ trans_choice('people.id', 2) }}</h3>
                            @if($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_GLOBAL)
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h4 class="border-bottom">{{ trans_choice('people.id.global',1) }}</h4>
                                    <a href="{{ route('people.school-ids.manage.global') }}" class="text-primary"><i class="fa-solid fa-edit"></i></a>
                                </div>
                                @if($idSettings->getGlobalId())
                                    <div class="mb-3">{!! $idSettings->getGlobalId()->preview !!}</div>
                                @else
                                    <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
                                @endif
                            @elseif($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_ROLES)
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h4 class="border-bottom">{{ __('people.id.student') }}</h4>
                                    <a href="{{ route('people.school-ids.manage.role', \App\Models\Utilities\SchoolRoles::StudentRole()) }}" class="text-primary"><i class="fa-solid fa-edit"></i></a>
                                </div>
                                @if($idSettings->getRoleId(\App\Models\Utilities\SchoolRoles::StudentRole())->preview)
                                    <div class="mb-3">{!! $idSettings->getRoleId(\App\Models\Utilities\SchoolRoles::StudentRole())->preview !!}</div>
                                @else
                                    <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
                                @endif

                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h4 class="border-bottom">{{ __('people.id.parent') }}</h4>
                                    <a href="{{ route('people.school-ids.manage.role', \App\Models\Utilities\SchoolRoles::ParentRole()) }}" class="text-primary"><i class="fa-solid fa-edit"></i></a>
                                </div>
                                @if($idSettings->getRoleId(\App\Models\Utilities\SchoolRoles::ParentRole())->preview)
                                <div class="mb-3">{!! $idSettings->getRoleId(\App\Models\Utilities\SchoolRoles::ParentRole())->preview !!}</div>
                                @else
                                    <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
                                @endif

                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h4 class="border-bottom">{{ __('people.id.employee') }}</h4>
                                    <a href="{{ route('people.school-ids.manage.role', \App\Models\Utilities\SchoolRoles::EmployeeRole()) }}" class="text-primary"><i class="fa-solid fa-edit"></i></a>
                                </div>
                                @if($idSettings->getRoleId(\App\Models\Utilities\SchoolRoles::EmployeeRole())->preview)
                                <div class="mb-3">{!! $idSettings->getRoleId(\App\Models\Utilities\SchoolRoles::EmployeeRole())->preview !!}</div>
                                @else
                                    <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
                                @endif
                            @elseif($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_CAMPUSES)
                                @foreach(\App\Models\Locations\Campus::all() as $campus)
                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <h4 class="border-bottom">{{ __('people.id.campus', ['campus' => $campus->name]) }}</h4>
                                        <a href="{{ route('people.school-ids.manage.campus', $campus) }}" class="text-primary"><i class="fa-solid fa-edit"></i></a>
                                    </div>
                                    @if($idSettings->getCampusId($campus)->preview)
                                    <div class="mb-3">{!! $idSettings->getCampusId($campus)->preview !!}</div>
                                    @else
                                        <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
                                    @endif
                                @endforeach
                            @elseif($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_BOTH)
                                @foreach(\App\Models\Locations\Campus::all() as $campus)
                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <h4 class="border-bottom">{{ __('people.id.both.student', ['campus' => $campus->name]) }}</h4>
                                        <a href="{{ route('people.school-ids.manage.both', ['role' => \App\Models\Utilities\SchoolRoles::StudentRole(), 'campus' => $campus]) }}" class="text-primary"><i class="fa-solid fa-edit"></i></a>
                                    </div>
                                    @if($idSettings->getRoleCampusId(\App\Models\Utilities\SchoolRoles::StudentRole(), $campus)->preview)
                                    <div class="mb-3">{!! $idSettings->getRoleCampusId(\App\Models\Utilities\SchoolRoles::StudentRole(), $campus)->preview !!}</div>
                                    @else
                                        <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
                                    @endif

                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <h4 class="border-bottom">{{ __('people.id.both.parent', ['campus' => $campus->name]) }}</h4>
                                        <a href="{{ route('people.school-ids.manage.both', [\App\Models\Utilities\SchoolRoles::ParentRole(), $campus]) }}" class="text-primary"><i class="fa-solid fa-edit"></i></a>
                                    </div>
                                    @if($idSettings->getRoleCampusId(\App\Models\Utilities\SchoolRoles::ParentRole(), $campus)->preview)
                                    <div class="mb-3">{!! $idSettings->getRoleCampusId(\App\Models\Utilities\SchoolRoles::ParentRole(), $campus)->preview !!}</div>
                                    @else
                                        <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
                                    @endif
                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <h4 class="border-bottom">{{ __('people.id.both.employee', ['campus' => $campus->name]) }}</h4>
                                        <a href="{{ route('people.school-ids.manage.both',[\App\Models\Utilities\SchoolRoles::EmployeeRole(), $campus]) }}" class="text-primary"><i class="fa-solid fa-edit"></i></a>
                                    </div>
                                    @if($idSettings->getRoleCampusId(\App\Models\Utilities\SchoolRoles::EmployeeRole(), $campus)->preview)
                                    <div class="mb-3">{!! $idSettings->getRoleCampusId(\App\Models\Utilities\SchoolRoles::EmployeeRole(), $campus)->preview !!}</div>
                                    @else
                                        <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
