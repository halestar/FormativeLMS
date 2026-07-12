@extends('layouts.app', ['breadcrumb' => $breadcrumb])
@inject('authSettings', 'App\\Classes\\Settings\\AuthSettings')
@section('content')
    <div class="container mb-5">
        <div class="row">
            {{-- Profile Image and Settings Column --}}
            <div class="col-md-4">
                <div class="d-flex flex-column">
                    {{-- Profile Image --}}
                    <div class="profile-img">
                        <img
                                class="img-fluid img-thumbnail"
                                src="{{ $person->portrait_url }}"
                                alt="{{ __('people.profile.image') }}"
                        />
                    </div>
                    {{-- Personal Settings and Links --}}
                    <div class="profile-work w-100">
                        @if($isSelf)
                            <p>{{ __('people.preferences') }}</p>
                            <a href="{{ route('people.school-ids.show') }}">{{ __('people.id.mine') }}</a><br/>
                            <a href="{{ route('people.preferences.communications') }}">{{ __('people.preferences.communications') }}</a>
                            <br/>
                            @if($person->authConnection?->canChangePassword())
                                <a href="{{ route('people.password') }}">{{ __('settings.auth.password.change') }}</a>
                                <br/>
                            @endif

                            <p>{{ __('integrators.integrations.available') }}</p>
                            <livewire:auth.user-integrations/>
                        @elseif($self->can('people.edit'))
                            <p>{{ __('people.preferences') }}</p>
                            <a href="{{ route('people.preferences.communications', $person->school_id) }}">{{ __('people.preferences.communications') }}</a>
                            <br/>
                        @endif
                    </div>
                </div>
            </div>
            {{-- Main Content Column --}}
            <div class="col-md-8">
                <div class="row mb-4">
                    {{-- Basic Info --}}
                    <div class="col-md-8">
                        <div class="profile-head d-flex align-items-start flex-column h-100">
                            <h5>
                                {{ $person->name }}
                            </h5>
                            <h6>
                                <div>
                                    <strong class="me-2">{{ __('settings.roles') }}
                                        :</strong> {{ $person->roles?->pluck('name')->join(', ') ?? __('settings.roles.no') }}
                                </div>
                            </h6>
                            @if($person->isEmployee())
                                <h6>
                                    <div>
                                        <strong class="me-2">{{ trans_choice('locations.campus',2) }}
                                            :</strong> {{ $person->employeeCampuses->pluck('name')->join(', ') }}
                                    </div>
                                </h6>
                                @if($person->isTeacher())
                                    <h6>
                                        <div>
                                            <strong class="me-2">
                                                {{ trans_choice('subjects.subject.taught', $person->subjectsTaught()->count()) }}
                                                :
                                            </strong>
                                            {{ $person->subjectsTaught->isEmpty()? __('subjects.subject.taught.no'):
                                                $person->subjectsTaught->map(fn($subject) => $subject->name . " (" . $subject->campus->abbr . ")")->join(', ') }}
                                        </div>
                                    </h6>
                                @endif
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
                                            @if(!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </div>
                                </h6>
                            @endif
                            <h6>
                                <div>
                                    <strong class="me-2">{{ __('people.profile.school_id') }}:</strong>
                                    {{ $person->school_id }}
                                </div>
                            </h6>
                        </div>
                    </div>
                    {{-- User Control --}}
                    <div class="col-md-4">
                        <div class="d-flex flex-column align-items-center">
                            @can('edit', $person)
                                <a type="button"
                                   class="btn btn-secondary profile-edit-btn mb-2"
                                   href="{{ route('people.edit', ['person' => $person->school_id]) }}"
                                >{{ __('people.profile.edit') }}</a>
                            @endcan
                            @can('people.impersonate')
                                @if($person->canBeImpersonated())
                                    <a type="button"
                                       class="btn btn-warning profile-edit-btn"
                                       href="{{ route('impersonate', $person->school_id) }}"
                                    >{{ __('people.impersonate') }}</a>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>

                {{-- Profile Tabs --}}
                <ul class="nav nav-tabs mt-auto mb-4" id="profile-tab" role="tablist">
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
                    @if($person->isSubstitute())
                        <li class="nav-item">
                            <a
                                    class="nav-link"
                                    id="tab-substitute"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-pane-substitute"
                                    href="#tab-pane-substitute"
                                    role="tab"
                                    aria-controls="#tab-pane-substitute"
                                    save-tab="substitute"
                                    aria-selected="false"
                            >{{  __('people.profile.substitute') }}</a>
                        </li>
                    @endif
                    @if($isSelf)
                        <li class="nav-item">
                            <a
                                    class="nav-link"
                                    id="tab-security"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-pane-security"
                                    href="#tab-pane-security"
                                    role="tab"
                                    aria-controls="#tab-pane-security"
                                    aria-selected="true"
                                    save-tab="security"
                            >{{ __('people.profile.security') }}</a>
                        </li>
                        <li class="nav-item">
                            <a
                                    class="nav-link"
                                    id="tab-privacy"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-pane-privacy"
                                    href="#tab-pane-privacy"
                                    role="tab"
                                    aria-controls="#tab-pane-privacy"
                                    aria-selected="true"
                                    save-tab="privacy"
                            >{{ __('people.profile.privacy') }}</a>
                        </li>
                    @endif
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content profile-tab" id="profile-tab-content">
                    <div
                            class="tab-pane fade show active"
                            id="tab-pane-basic" role="tabpanel" aria-labelledby="tab-basic" tabindex="1"
                    >
                        <x-people.basic-info-fields :person="$person"/>
                    </div>
                    @if($person->isStudent() || $person->isTeacher())
                        <div
                                class="tab-pane fade"
                                id="tab-pane-schedule" role="tabpanel" aria-labelledby="tab-schedule"
                                tabindex="1"
                        >
                            @if($person->isStudent())
                                <x-schedule-viewer :schedule-sources="$person->student()->classSessions" :width="700"/>
                            @else
                                <x-schedule-viewer :schedule-sources="$person->currentClassSessions" :width="700"/>
                            @endif
                        </div>
                    @endif
                    @if($person->isSubstitute())
                        <div
                                class="tab-pane fade"
                                id="tab-pane-substitute" role="tabpanel" aria-labelledby="tab-substitute"
                                tabindex="1"
                        >
                            <x-people.substitute-info-fields :person="$person"/>
                        </div>
                    @endif

                    @if($isSelf)
                        <div
                                class="tab-pane fade"
                                id="tab-pane-security" role="tabpanel" aria-labelledby="tab-security"
                                tabindex="1"
                        >
                            <div class="mb-3">
                                @if($authSettings->passkeys_allow)
                                    @if($authSettings->passkeys_require && $person->passkeys->isEmpty())
                                        <div class="alert alert-warning shadow-sm" role="alert">
                                            {!! __('auth.passkey.required') !!}
                                        </div>
                                    @endif

                                    <livewire:passkeys/>
                                @endif
                            </div>
                            <livewire:people.mfa/>
                        </div>

                        <div
                                class="tab-pane fade"
                                id="tab-pane-privacy" role="tabpanel" aria-labelledby="tab-privacy"
                                tabindex="1"
                        >

                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
