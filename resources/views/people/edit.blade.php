@extends('layouts.app', ['breadcrumb' => $breadcrumb])
@use('\App\Classes\People\RoleField')
@section('content')
    <div class="container">
        <div class="row">
            {{-- Profile Image and Settings Column --}}
            <div class="col-md-4">
                <div class="d-flex flex-column">
                    {{-- Profile Image --}}
                    <livewire:people.portrait-editor :person="$person"/>
                    {{-- Personal Settings and Links --}}
                    <div class="profile-work">
                        <livewire:auth.user-auth-manager :person="$person->model"/>
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
                            <livewire:role-assigner :attachObj="$person->model"/>
                            @if($person->isEmployee())
                                <livewire:people.campus-assigner :person="$person->model"/>
                                @if($person->isTeacher())
                                    <livewire:people.subject-assigner :teacher="$person->model"/>
                                @endif
                            @endif
                            @if($person->isStudent() || $person->hasRole(\App\Models\Utilities\SchoolRoles::$OLD_STUDENT))
                                <livewire:people.student-record-manager :person="$person->model"/>
                            @endif
                        </div>
                    </div>
                    {{-- User Control --}}
                    <div class="col-md-4">
                        <div class="d-flex flex-column align-items-center">
                            <a
                                    role="button"
                                    class="btn btn-danger profile-edit-btn"
                                    href="{{ route('people.show', ['person' => $person->school_id]) }}"
                            >{{ __('people.profile.editing') }}</a>
                        </div>
                    </div>
                </div>
                {{-- Profile Tabs --}}
                <ul class="nav nav-tabs mt-auto mb-4" id="profile-tab" role="tablist">
                    <li class="nav-item">
                        <a
                                class="nav-link active"
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
                    <li class="nav-item">
                        <a
                                class="nav-link"
                                id="tab-contact"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-pane-contact"
                                href="#tab-pane-contact"
                                role="tab"
                                aria-controls="#tab-pane-contact"
                                aria-selected="true"
                                save-tab="contact"
                        >{{ __('people.profile.contact') }}</a>
                    </li>
                    <li class="nav-item">
                        <a
                                class="nav-link"
                                id="tab-relationships"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-pane-relationships"
                                href="#tab-pane-relationships"
                                role="tab"
                                aria-controls="#tab-pane-relationships"
                                aria-selected="true"
                                save-tab="relationships"
                        >{{ __('people.relationships') }}</a>
                    </li>
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
                                    aria-selected="true"
                                    save-tab="substitute"
                            >{{ __('people.profile.substitute') }}</a>
                        </li>
                    @endif
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content profile-tab" id="profile-tab-content">
                    <div
                            class="tab-pane fade show active"
                            id="tab-pane-basic"
                            role="tabpanel"
                            aria-labelledby="tab-basic"
                            tabindex="0"
                    >
                        <form action="{{ route('people.update', ['person' => $person->school_id]) }}"
                              method="POST">
                            @csrf
                            @method('PUT')
                            <ul class="list-group">
                                @if($person->canEdit('first') || $person->canView('first'))
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="first"
                                                   class="col-form-label">{{ __('people.profile.fields.first') }}</label>
                                            <span class="w-50">
                                                <input type="text" name="first" id="first" value="{{ $person->first }}"
                                                       @readonly(!$person->canEdit('first'))
                                                       class="form-control form-control-sm text-end @error('first') is-invalid @enderror"
                                                />
                                                <x-utilities.error-display
                                                        key="first">{{ $errors->first('first') }}</x-utilities.error-display>
                                            </span>
                                        </div>
                                    </li>
                                @endif
                                @if($person->canEdit('middle') || $person->canView('middle'))
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="middle"
                                                   class="col-form-label">{{ __('people.profile.fields.middle') }}</label>
                                            <span class="w-50">
                                                <input type="text" name="middle" id="middle"
                                                       value="{{ $person->middle }}"
                                                       @readonly(!$person->canEdit('middle'))
                                                       class="form-control form-control-sm text-end @error('middle') is-invalid @enderror"
                                                />
                                                <x-utilities.error-display
                                                        key="middle">{{ $errors->first('middle') }}</x-utilities.error-display>
                                            </span>
                                        </div>
                                    </li>
                                @endif
                                @if($person->canEdit('last') || $person->canView('last'))
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="last"
                                                   class="col-form-label">{{ __('people.profile.fields.last') }}</label>
                                            <span class="w-50">
                                                <input
                                                        type="text"
                                                        name="last"
                                                        id="last"
                                                        value="{{ $person->last }}"
                                                        @readonly(!$person->canEdit('last'))
                                                        class="form-control form-control-sm @error('last') is-invalid @enderror text-end"
                                                />
                                                <x-utilities.error-display
                                                        key="last">{{ $errors->first('last') }}</x-utilities.error-display>
                                            </span>
                                        </div>
                                    </li>
                                @endif
                                @if($person->canEdit('nick') || $person->canView('nick'))
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="nick"
                                                   class="col-form-label">{{ __('people.profile.fields.nick') }}</label>
                                            <span class="w-50">
                                                <input type="text" name="nick" id="nick" value="{{ $person->nick }}"
                                                       @readonly(!$person->canEdit('nick'))
                                                       class="form-control form-control-sm text-end @error('nick') is-invalid @enderror text-end"
                                                />
                                                <x-utilities.error-display
                                                        key="nick">{{ $errors->first('nick') }}</x-utilities.error-display>
                                            </span>
                                        </div>
                                    </li>
                                @endif
                                @if($person->canEdit('email') || $person->canView('email'))
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="email"
                                                   class="col-form-label">{{ __('people.profile.fields.email') }}</label>
                                            <span class="w-50">
                                            <input type="email" name="email" id="email" value="{{ $person->email }}"
                                                   @readonly(!$person->canEdit('email'))
                                                   class="form-control form-control-sm text-end @error('email') is-invalid @enderror "
                                            />
                                                <x-utilities.error-display
                                                        key="email">{{ $errors->first('email') }}</x-utilities.error-display>
                                            </span>
                                        </div>
                                    </li>
                                @endif
                                @if($person->canEdit('dob') || $person->canView('dob'))
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="dob"
                                                   class="col-form-label">{{ __('people.profile.fields.dob') }}</label>
                                            <span class="w-50">
                                                <input type="date" name="dob" id="dob"
                                                       value="{{ $person->dob? $person->dob->format('Y-m-d'): "" }}"
                                                       @readonly(!$person->canEdit('dob'))
                                                       class="form-control form-control-sm text-end @error('dob') is-invalid @enderror"
                                                />
                                                <x-utilities.error-display
                                                        key="dob">{{ $errors->first('dob') }}</x-utilities.error-display>
                                            </span>
                                        </div>
                                    </li>
                                @endif
                                @foreach($person->schoolRoles as $role)
                                    @continue(empty($role->fields))
                                    @foreach($role->fields as $field)
                                        @php
                                            $fieldId = 'role_fields.' .$field->fieldId;
                                        @endphp
                                        @if($person->canEdit($fieldId) || $person->canView($fieldId))
                                            <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <label for="{{ $fieldId }}" class="col-form-label">
                                                        {{ $field->fieldName }}
                                                    </label>
                                                    <span class="w-50">
                                                        @switch($field->fieldType)
                                                            @case(RoleField::DATE)
                                                            @case(RoleField::DATETIME)
                                                            @case(RoleField::TEXT)
                                                                <input type="@if($field->fieldType === RoleField::DATE)
                                                                                date
                                                                            @elseif($field->fieldType == RoleField::DATETIME)
                                                                                datetime-local
                                                                            @elseif($field->fieldType == RoleField::URL)
                                                                                url
                                                                            @elseif($field->fieldType == RoleField::EMAIL)
                                                                                email
                                                                            @else
                                                                                text
                                                                            @endif"
                                                                       name="{{ $fieldId }}"
                                                                       id="{{ $fieldId }}"
                                                                       class="form-control form-control-sm text-end @error($fieldId) is-invalid @enderror"
                                                                       value="{{ $person->role_fields->{$field->fieldId} }}"
                                                                       @readonly(!$person->canEdit($fieldId))
                                                                       placeholder="{{ $field->fieldPlaceholder }}"
                                                                />
                                                                @break
                                                            @case(RoleField::SELECT)
                                                                <select type="text"
                                                                        name="{{ $fieldId }}"
                                                                        id="{{ $fieldId }}"
                                                                        class="form-select form-select-sm text-end @error($fieldId) is-invalid @enderror"
                                                                        @readonly(!$person->canEdit($fieldId))
                                                                >
                                                                    <option value=""></option>
                                                                    @foreach ($field->fieldOptions as $option)
                                                                        <option value="{{ $option }}" @selected($person->role_fields->{$field->fieldId} == $option)>
                                                                            {{ $option }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @break
                                                            @case(RoleField::CHECKBOX)
                                                                @foreach ($field->fieldOptions as $option)
                                                                    <div class="form-check">
                                                                      <input class="form-check-input"
                                                                             type="checkbox"
                                                                             value="{{ $option }}"
                                                                             name="{{ $fieldId }}[]"
                                                                             id="{{ $fieldId . "_" . $option }}"
                                                                      />
                                                                      <label class="form-check-label"
                                                                             for="{{ $fieldId . "_" . $option }}">
                                                                        {{ $option }}
                                                                      </label>
                                                                    </div>
                                                                @endforeach
                                                                @break
                                                            @case(RoleField::RADIO)
                                                                @foreach ($field->fieldOptions as $option)
                                                                    <div class="form-check">
                                                                      <input class="form-check-input"
                                                                             type="radio"
                                                                             value="{{ $option }}"
                                                                             name="{{ $fieldId }}"
                                                                             id="{{ $fieldId . "_" . $option }}"
                                                                      />
                                                                      <label class="form-check-label"
                                                                             for="{{ $fieldId . "_" . $option }}">
                                                                        {{ $option }}
                                                                      </label>
                                                                    </div>
                                                                @endforeach
                                                                @break
                                                            @case(RoleField::TEXTAREA)
                                                                <textarea name="{{ $fieldId }}"
                                                                          id="{{ $fieldId }}"
                                                                          class="form-control form-control-sm text-end @error($fieldId) is-invalid @enderror"
                                                                          @readonly(!$person->canEdit($fieldIdd))
                                                                          placeholder="{{ $field->fieldPlaceholder }}"
                                                                >{{ $person->role_fields->{$field->fieldId} }}</textarea>
                                                                @break
                                                        @endswitch
                                                    </span>
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                @endforeach
                            </ul>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary"
                                        type="submit">{{ __('people.profile.fields.update') }}</button>
                            </div>
                        </form>
                    </div>
                    <div
                            class="tab-pane fade"
                            id="tab-pane-contact"
                            aria-labelledby="tab-contact"
                            role="tabpanel"
                            tabindex="0"
                    >
                        <div class="mb-3 p-1">
                            @if($person->canView('addresses')|| $person->canEdit('addresses'))
                                <livewire:address-editor :addressable="$person"/>
                            @endif
                        </div>
                        <div class="mb-3 p-1">
                            @if($person->canView('addresses') || $person->canEdit('phones'))
                                <livewire:phone-editor :phoneable="$person"/>
                            @endif
                        </div>
                    </div>
                    <div
                            class="tab-pane fade"
                            id="tab-pane-relationships"
                            aria-labelledby="tab-relationships"
                            role="tabpanel"
                            tabindex="0"
                    >
                        <div class="mb-3 p-1">
                            @if($person->canView('addresses') || $person->canEdit('relationships'))
                                <livewire:relationship-creator :person="$person->model"/>
                            @endif
                        </div>
                    </div>
                    @if($person->isSubstitute())
                        <div
                                class="tab-pane fade"
                                id="tab-pane-substitute"
                                aria-labelledby="tab-substitute"
                                role="tabpanel"
                                tabindex="0"
                        >
                            <livewire:people.campus-assigner :person="$person->substituteProfile"/>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
