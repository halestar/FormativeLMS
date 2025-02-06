@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="row profile-head-row">
            <div class="col-md-4">
                <div class="profile-img">
                    <img
                        class="img-fluid img-thumbnail"
                        src="{{ $person->portrait_url }}"
                        alt="{{ __('people.profile.image') }}"
                    />
                </div>
            </div>
            <div class="col-md-6">
                <div class="profile-head d-flex align-items-start flex-column h-100">
                    <h5>
                        {{ $person->name }}
                    </h5>
                    <h6>
                        <div>
                            <strong class="me-2">{{ __('settings.roles') }}:</strong> {{ $person->roles->pluck('name')->join(', ') }}
                        </div>
                    </h6>
                    @if($person->isEmployee())
                        <h6>
                            <div>
                                <strong class="me-2">Campuses:</strong> {{ $person->employeeCampuses->pluck('name')->join(', ') }}
                            </div>
                        </h6>
                    @endif
                    @if($person->isStudent())
                        <h6>
                            <div>
                                <strong class="me-2">{{ trans_choice('crud.level', 1) }}:</strong>
                                @if($person->student())
                                    {{ $person->student()->level->name }}
                                    ({{ $person->student()->campus->name }})
                                @endif
                            </div>
                        </h6>
                    @endif
                    @if($person->isParent() && $person->currentChildStudents()->count() > 0)
                        <h6>
                            <div>
                                <strong class="me-2">Students:</strong>
                                @foreach($person->currentChildStudents() as $student)
                                    <a href="{{ route('people.show', ['person' => $student->person_id]) }}">
                                        {{ $student->person->name }}
                                        ({{ $student->level->name }}, {{ $student->campus->name }})
                                    </a>
                                    @if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </h6>
                    @endif
                    <ul class="nav nav-tabs mt-auto" id="profile-tab" role="tablist">
                        <li class="nav-item">
                            <a
                                class="nav-link active "
                                id="tab-basic"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-pane-basic"
                                href="#tab-pane-basic"
                                role="tab"
                                aria-controls="#tab-pane-basic"
                                aria-selected="true"
                                save-tab="basic"
                            >{{ __('people.profile.basic') }}</a>
                        </li>
                        @foreach($person->roles as $role)
                            @if(count($role->fields) > 0)
                                <li class="nav-item">
                                    <a
                                        class="nav-link"
                                        id="tab-role-{{ $role->id }}"
                                        data-bs-toggle="tab"
                                        data-bs-target="#tab-pane-role-{{ $role->id }}"
                                        href="#tab-pane-role-{{ $role->id }}"
                                        role="tab"
                                        aria-controls="#tab-pane-role-{{ $role->id }}"
                                        save-tab="role-{{ $role->id }}"
                                        aria-selected="false"
                                    >{{ $role->name }}</a>
                                </li>
                            @endif
                        @endforeach
                        @if($person->isStudent() || $person->isTeacher())
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    id="tab-schedule"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-pane-schedule"
                                    href="#tab-pane-schedule"
                                    role="tab"
                                    aria-controls="#tab-pane-schedule"
                                    save-tab="schedule"
                                    aria-selected="false"
                                >{{ $person->isStudent()? __('people.profile.schedule.student'): __('people.profile.schedule.teacher') }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            @can('edit', $person)
            <div class="col-md-2">
                <a type="button" class="btn btn-secondary profile-edit-btn" href="{{ route('people.edit', ['person' => $person->id]) }}">{{ __('people.profile.edit') }}</a>
            </div>
            @endcan
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="profile-work">
                    @if($isSelf)
                    <p>{{ __('people.profile.links.groups.settings') }}</p>
                    <a href="">Link 2</a><br/>
                    <a href="">Link 3</a><br/>
                    @endif
                    <p>Link Group 2</p>
                    <a href="">Link 1</a><br/>
                    <a href="">Link 2</a><br/>
                    <a href="">Link 3</a><br/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="tab-content profile-tab" id="profile-tab-content">
                    <div
                        class="tab-pane fade show active"
                        id="tab-pane-basic" role="tabpanel" aria-labelledby="tab-basic" tabindex="1"
                    >
                        <ul class="list-group">
                            @if($person->first)
                            <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label>{{ __('people.profile.fields.first') }}</label>
                                    <span>{{ $person->first }}</span>
                                </div>
                            </li>
                            @endif
                            @if($person->middle)
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label>{{ __('people.profile.fields.middle') }}</label>
                                        <span>{{ $person->middle }}</span>
                                    </div>
                                </li>
                            @endif
                            @if($person->last)
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label>{{ __('people.profile.fields.last') }}</label>
                                        <span>{{ $person->last }}</span>
                                    </div>
                                </li>
                            @endif
                            @if($person->email)
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label>{{ __('people.profile.fields.email') }}</label>
                                        <span>{{ $person->email }}</span>
                                    </div>
                                </li>
                            @endif
                            @if($person->nick)
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label>{{ __('people.profile.fields.nick') }}</label>
                                        <span>{{ $person->nick }}</span>
                                    </div>
                                </li>
                            @endif
                            @if($person->dob)
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label>{{ __('people.profile.fields.dob') }}</label>
                                        <span>{{ $person->dob->format(config('lms.date_format')) }}</span>
                                    </div>
                                </li>
                            @endif
                        </ul>
                        @if($self->canViewField('addresses', $person))
                            @foreach($person->addresses as $address)
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-top">
                                        <label>
                                            @if($address->personal->primary) {{ __('addresses.primary') }} @endif
                                            @if($address->personal->work) {{ __('addresses.work') }} @endif
                                            @if($address->personal->seasonal)
                                                {{ __('addresses.seasonal_address', ['season_start' => $address->personal->season_start, 'season_end' => $address->personal->season_end]) }}
                                            @endif
                                            {{ __('addresses.address') }}:
                                        </label>
                                        <span>{!! nl2br($address->prettyAddress) !!}</span>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                        @if($self->canViewField('phones', $person))
                            @foreach($person->phones as $phone)
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-top">
                                        <label>
                                            @if($phone->personal->primary) {{ __('addresses.primary') }} @endif
                                            @if($phone->personal->work) {{ __('addresses.work') }} @endif
                                            @if($phone->mobile) {{ __('phones.mobile') }} @endif
                                            {{ __('phones.phone') }}:
                                        </label>
                                        <span>{!! $phone->prettyPhone !!}</span>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                        @if($self->canViewField('relationships', $person))
                            @foreach($person->relationships as $relation)
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-top">
                                        <label>
                                            {{ ($relation->personal->relationship? $relation->personal->relationship->name: "?") . " " . __('common.to') }}
                                        </label>
                                        <span>
                                            <a href="{{ route('people.show', ['person' => $relation->id]) }}">
                                                {{ $relation->name }}
                                            </a>
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </div>
                    @foreach($person->schoolRoles as $role)
                        @if(count($role->fields) > 0)
                            <div
                                class="tab-pane fade"
                                id="tab-pane-role-{{ $role->id }}" role="tabpanel" aria-labelledby="tab-role-{{ $role->id }}"
                                tabindex="{{ $loop->iteration + 10 }}"
                            >
                                <ul class="list-group">
                                    @foreach($role->fields as $field)
                                        @if($self->canViewField($field, $person) && $field->fieldValue)
                                        <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label>{{ $field->fieldName }}</label>
                                                <span>{{ is_array($field->fieldValue)? implode(", ", $field->fieldValue): $field->fieldValue }}</span>
                                            </div>
                                        </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach

                    @if($person->isStudent() || $person->isTeacher())
                        <div
                            class="tab-pane fade"
                            id="tab-pane-schedule" role="tabpanel" aria-labelledby="tab-schedule"
                            tabindex="1"
                        >
                            @if($person->isStudent())
                            <x-schedule-viewer :schedule-sources="$person->student()->classSessions" :width="700" />
                            @else
                                <x-schedule-viewer :schedule-sources="$person->currentClassSessions" :width="700" />
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
