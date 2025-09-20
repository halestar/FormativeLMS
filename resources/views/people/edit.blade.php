@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
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
                        @if(!$isSelf || $self->canEditOwnField('portrait'))
                            <form id="portrait_form"
                                  action="{{ route('people.update.portrait', ['person' => $person->school_id]) }}"
                                  method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="file btn btn-lg btn-dark">
                                    {{ __('people.profile.image.update') }}
                                    <input type="file" name="portrait" onchange="$('#portrait_form').submit()"/>
                                </div>
                            </form>
                            @if($person->hasPortrait())
                                <button
                                        class="remove btn btn-lg btn-danger"
                                        onclick="confirmDelete('{{ __('people.profile.image.remove.confirm') }}', '{{ route('people.delete.portrait', ['person' => $person->school_id]) }}')"
                                >
                                    {{ __('people.profile.image.remove') }}
                                </button>
                            @endif
                        @endif
                    </div>
                    {{-- Personal Settings and Links --}}
                    <div class="profile-work">
                        <livewire:auth.user-auth-manager :person="$person"/>
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
                            <livewire:role-assigner :attachObj="$person"/>
                            @if($person->isEmployee())
                                <livewire:people.campus-assigner :person="$person"/>
                            @endif
                            @if($person->isStudent() || $person->hasRole(\App\Models\Utilities\SchoolRoles::$OLD_STUDENT))
                                <livewire:people.student-record-manager :person="$person"/>
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
                                        aria-selected="false"
                                        save-tab="role-{{ $role->id }}"
                                >{{ $role->name }}</a>
                            </li>
                        @endif
                    @endforeach
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
                        <form action="{{ route('people.update.basic', ['person' => $person->school_id]) }}"
                              method="POST">
                            @csrf
                            @method('PUT')
                            <ul class="list-group">
                                @if(!$isSelf || $self->canEditOwnField('first'))
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="first"
                                                   class="col-form-label">{{ __('people.profile.fields.first') }}</label>
                                            <span class="w-50">
                                            <input type="text" name="first" id="first" value="{{ $person->first }}"
                                                   class="form-control form-control-sm text-end"/>
                                        </span>
                                        </div>
                                    </li>
                                @endif
                                @if(!$isSelf || $self->canEditOwnField('middle'))
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="middle"
                                                   class="col-form-label">{{ __('people.profile.fields.middle') }}</label>
                                            <span class="w-50">
                                            <input type="text" name="middle" id="middle" value="{{ $person->middle }}"
                                                   class="form-control form-control-sm text-end"/>
                                        </span>
                                        </div>
                                    </li>
                                @endif
                                @if(!$isSelf || $self->canEditOwnField('last'))
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
                                                    class="form-control form-control-sm @error('last') is-invalid @enderror text-end"/>
                                            <x-error-display key="last">{{ $errors->first('last') }}</x-error-display>
                                        </span>
                                        </div>
                                    </li>
                                @endif
                                @if(!$isSelf || $self->canEditOwnField('nick'))
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="nick"
                                                   class="col-form-label">{{ __('people.profile.fields.nick') }}</label>
                                            <span class="w-50">
                                            <input type="text" name="nick" id="nick" value="{{ $person->nick }}"
                                                   class="form-control form-control-sm text-end"/>
                                        </span>
                                        </div>
                                    </li>
                                @endif
                                @if(!$isSelf || $self->canEditOwnField('email'))
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="email"
                                                   class="col-form-label">{{ __('people.profile.fields.email') }}</label>
                                            <span class="w-50">
                                            <input type="email" name="email" id="email" value="{{ $person->email }}"
                                                   class="form-control form-control-sm text-end"/>
                                        </span>
                                        </div>
                                    </li>
                                @endif
                                @if(!$isSelf || $self->canEditOwnField('dob'))
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="dob"
                                                   class="col-form-label">{{ __('people.profile.fields.dob') }}</label>
                                            <span class="w-50">
                                            <input type="date" name="dob" id="dob"
                                                   value="{{ $person->dob? $person->dob->format('Y-m-d'): "" }}"
                                                   class="form-control form-control-sm text-end"/>
                                        </span>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                            <div class="row">
                                <button type="submit"
                                        class="btn btn-primary col mt-3">{{ __('people.profile.basic.update') }}</button>
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
                            @if(!$isSelf || $self->canEditOwnField('addresses'))
                                <livewire:address-editor :addressable="$person"/>
                            @endif
                        </div>
                        <div class="mb-3 p-1">
                            @if(!$isSelf || $self->canEditOwnField('phones'))
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
                            @if(!$isSelf || $self->canEditOwnField('relationships'))
                                <livewire:relationship-creator :person="$person"/>
                            @endif
                        </div>
                    </div>
                    @foreach($person->schoolRoles as $role)
                        @if(count($role->fields) > 0)
                            <div
                                    class="tab-pane fade"
                                    id="tab-pane-role-{{ $role->id }}" role="tabpanel"
                                    aria-labelledby="tab-role-{{ $role->id }}"
                                    tabindex="{{ $loop->iteration + 10 }}"
                            >
                                <form action="{{ route('people.roles.fields.update', ['person' => $person->school_id, 'role' => $role->id]) }}"
                                      method="POST">
                                    @csrf
                                    @method('PUT')
                                    <ul class="list-group my-3">
                                        @foreach($role->fields as $field)
                                            @if(!$isSelf || $self->canEditOwnField($field))
                                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                                    {!! $field->getHTML() !!}
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary"
                                                type="submit">{{ __('people.profile.fields.update') }}</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endforeach
                </div>

            </div>
        </div>
    </div>
@endsection
