@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="row profile-head-row">
            <div class="col-md-4">
                <div class="profile-img">
                    <form>
                        <img
                            class="img-fluid img-thumbnail"
                            @if($person->portrait_url)
                                src="{{ $person->portrait_url }}"
                            @else
                                src='data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>'
                            @endif
                            alt="{{ __('people.profile.image') }}"
                        />
                        <div class="file btn btn-lg btn-primary">
                            {{ __('people.profile.image.update') }}
                            <input type="file" name="portrait"/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="profile-head d-flex align-items-start flex-column h-100">
                    <h5>
                        {{ $person->name }}
                    </h5>
                    <h6>
                        {{ $person->roles->pluck('name')->join(', ') }}
                    </h6>
                    <ul class="nav nav-tabs mt-auto" id="profile-tab" role="tablist">
                        @foreach(\App\Models\CRUD\ViewableGroup::viewable()->get() as $tab)
                            <li class="nav-item">
                                <a
                                    class="nav-link @if($loop->first) active @endif"
                                    id="tab-{{ $tab->id }}"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-pane-{{ $tab->id }}"
                                    href="#tab-pane-{{ $tab->id }}"
                                    role="tab"
                                    aria-controls="#tab-pane-{{ $tab->id }}"
                                    aria-selected="true"
                                >{{ $tab->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-2">
                <a type="button" class="btn btn-danger profile-edit-btn" href="{{ route('people.show', ['person' => $person->id]) }}">{{ __('people.profile.editing') }}</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="profile-work">
                    <p>Link Group 2</p>
                    <a href="">Link 1</a><br/>
                    <a href="">Link 2</a><br/>
                    <a href="">Link 3</a><br/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="tab-content profile-tab" id="profile-tab-content">
                    <div class="tab-pane fade show active" id="basic-tab-pane" role="tabpanel" aria-labelledby="basic-tab" tabindex="0">
                        <form action="{{ route('people.update.basic', ['person' => $person->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <ul class="list-group">
                                @if($self->canViewField($self->viewableField('first'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="first" class="col-form-label">{{ __('people.profile.fields.first') }}</label>
                                        @if($self->canEditField($self->viewableField('first')))
                                        <span class="w-50">
                                            <input type="text" name="first" id="first" value="{{ $person->first }}" class="form-control form-control-sm text-end" />
                                        </span>
                                        @else
                                        <span>{{ $person->first }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('middle'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="middle" class="col-form-label">{{ __('people.profile.fields.middle') }}</label>
                                        @if($self->canEditField($self->viewableField('middle')))
                                        <span class="w-50">
                                            <input type="text" name="middle" id="middle" value="{{ $person->middle }}" class="form-control form-control-sm text-end" />
                                        </span>
                                        @else
                                            <span>{{ $person->middle }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('middle'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="last" class="col-form-label">{{ __('people.profile.fields.last') }}</label>
                                        @if($self->canEditField($self->viewableField('last')))
                                        <span class="w-50">
                                            <input
                                                type="text"
                                                name="last"
                                                id="last"
                                                value="{{ $person->last }}"
                                                class="form-control form-control-sm @error('last') is-invalid @enderror text-end" />
                                            <x-error-display key="last">{{ $errors->first('last') }}</x-error-display>
                                        </span>
                                        @else
                                            <span>{{ $person->last }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('nick'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="nick" class="col-form-label">{{ __('people.profile.fields.nick') }}</label>
                                        @if($self->canEditField($self->viewableField('nick')))
                                        <span class="w-50">
                                            <input type="text" name="nick" id="nick" value="{{ $person->nick }}" class="form-control form-control-sm text-end" />
                                        </span>
                                        @else
                                            <span>{{ $person->nick }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('email'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="email" class="col-form-label">{{ __('people.profile.fields.email') }}</label>
                                        @if($self->canEditField($self->viewableField('email')))
                                        <span class="w-50">
                                            <input type="email" name="email" id="email" value="{{ $person->email }}" class="form-control form-control-sm text-end" />
                                        </span>
                                        @else
                                            <span>{{ $person->email }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('dob'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="dob" class="col-form-label">{{ __('people.profile.fields.dob') }}</label>
                                        @if($self->canEditField($self->viewableField('dob')))
                                        <span class="w-50">
                                            <input type="date" name="dob" id="dob" value="{{ $person->dob->format('Y-m-d') }}" class="form-control form-control-sm text-end" />
                                        </span>
                                        @else
                                            <span>{{ $person->dob->format('Y-m-d') }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('ethnicity'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="ethnicity_id" class="col-form-label">{{ __('people.profile.fields.ethnicity') }}</label>
                                        @if($self->canEditField($self->viewableField('ethnicity')))
                                        <span class="w-50">
                                            <select name="ethnicity_id" id="ethnicity_id" class="form-select text-end">
                                                <option value="">{{ __('people.profile.fields.ethnicity.none') }}</option>
                                                {!! \App\Models\CRUD\Ethnicity::htmlOptions($person->ethnicity) !!}
                                            </select>
                                        </span>
                                        @else
                                            <span>{{ $person->ethnicity }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('title'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="title_id" class="col-form-label">{{ __('people.profile.fields.title') }}</label>
                                        @if($self->canEditField($self->viewableField('title')))
                                        <span class="w-50">
                                            <select name="title_id" id="title_id" class="form-select text-end">
                                                <option value="">{{ __('people.profile.fields.title.none') }}</option>
                                                {!! \App\Models\CRUD\Title::htmlOptions($person->title) !!}
                                            </select>
                                        </span>
                                        @else
                                            <span>{{ $person->title }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('suffix'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="suffix_id" class="col-form-label">{{ __('people.profile.fields.suffix') }}</label>
                                        @if($self->canEditField($self->viewableField('suffix')))
                                        <span class="w-50">
                                            <select name="suffix_id" id="suffix_id" class="form-select text-end">
                                                <option value="">{{ __('people.profile.fields.suffix.none') }}</option>
                                                {!! \App\Models\CRUD\Suffix::htmlOptions($person->suffix) !!}
                                            </select>
                                        </span>
                                        @else
                                            <span>{{ $person->suffix }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('honors'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="honors_id" class="col-form-label">{{ __('people.profile.fields.honors') }}</label>
                                        @if($self->canEditField($self->viewableField('honors')))
                                        <span class="w-50">
                                            <select name="honors_id" id="honors_id" class="form-select text-end">
                                                <option value="">{{ __('people.profile.fields.honors.none') }}</option>
                                                {!! \App\Models\CRUD\Honors::htmlOptions($person->honors) !!}
                                            </select>
                                        </span>
                                        @else
                                            <span>{{ $person->honors }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('gender'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="gender_id" class="col-form-label">{{ __('people.profile.fields.gender') }}</label>
                                        @if($self->canEditField($self->viewableField('gender')))
                                        <span class="w-50">
                                            <select name="gender_id" id="gender_id" class="form-select text-end">
                                                <option value="">{{ __('people.profile.fields.gender.none') }}</option>
                                                {!! \App\Models\CRUD\Gender::htmlOptions($person->gender) !!}
                                            </select>
                                        </span>
                                        @else
                                            <span>{{ $person->gender }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('pronouns'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="pronoun_id" class="col-form-label">{{ __('people.profile.fields.pronouns') }}</label>
                                        @if($self->canEditField($self->viewableField('pronouns')))
                                        <span class="w-50">
                                            <select name="pronoun_id" id="pronoun_id" class="form-select text-end">
                                                <option value="">{{ __('people.profile.fields.pronouns.none') }}</option>
                                                {!! \App\Models\CRUD\Pronouns::htmlOptions($person->pronouns) !!}
                                            </select>
                                        </span>
                                        @else
                                            <span>{{ $person->pronouns }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('occupation'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="occupation" class="col-form-label">{{ __('people.profile.fields.occupation') }}</label>
                                        @if($self->canEditField($self->viewableField('occupation')))
                                        <span class="w-50">
                                            <input type="text" name="occupation" id="occupation" value="{{ $person->occupation }}" class="form-control form-control-sm text-end" />
                                        </span>
                                        @else
                                            <span>{{ $person->occupation }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('job_title'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="job_title" class="col-form-label">{{ __('people.profile.fields.job_title') }}</label>
                                        @if($self->canEditField($self->viewableField('job_title')))
                                        <span class="w-50">
                                            <input type="text" name="job_title" id="job_title" value="{{ $person->job_title }}" class="form-control form-control-sm text-end" />
                                        </span>
                                        @else
                                            <span>{{ $person->job_title }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('work_company'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="work_company" class="col-form-label">{{ __('people.profile.fields.work_company') }}</label>
                                        @if($self->canEditField($self->viewableField('work_company')))
                                        <span class="w-50">
                                            <input type="text" name="work_company" id="work_company" value="{{ $person->work_company }}" class="form-control form-control-sm text-end" />
                                        </span>
                                        @else
                                            <span>{{ $person->work_company }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('salutation'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="salutation" class="col-form-label">{{ __('people.profile.fields.salutation') }}</label>
                                        @if($self->canEditField($self->viewableField('salutation')))
                                        <span class="w-50">
                                            <input type="text" name="salutation" id="salutation" value="{{ $person->salutation }}" class="form-control form-control-sm text-end" />
                                        </span>
                                        @else
                                            <span>{{ $person->salutation }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                @if($self->canViewField($self->viewableField('family_salutation'),$person))
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="family_salutation" class="col-form-label">{{ __('people.profile.fields.family_salutation') }}</label>
                                        @if($self->canEditField($self->viewableField('family_salutation')))
                                        <span class="w-50">
                                            <input type="text" name="family_salutation" id="family_salutation" value="{{ $person->family_salutation }}" class="form-control form-control-sm text-end" />
                                        </span>
                                        @else
                                            <span>{{ $person->family_salutation }}</span>
                                        @endif
                                    </div>
                                </li>
                                @endif
                            </ul>
                            <div class="row">
                                <button type="submit" class="btn btn-primary col mt-3">{{ __('people.profile.basic.update') }}</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="1">
                        content here
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
