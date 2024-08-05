@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="row profile-head-row">
            <div class="col-md-4">
                <div class="profile-img">
                    <img
                        class="img-fluid img-thumbnail"
                        @if($person->portrait_url)
                            src="{{ $person->portrait_url }}"
                        @else
                            src='data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>'
                        @endif
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
                        {{ $person->roles->pluck('name')->join(', ') }}
                    </h6>
                    <ul class="nav nav-tabs mt-auto" id="profile-tab" role="tablist">
                        <li class="nav-item">
                            <a
                                class="nav-link active"
                                id="basic-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#basic-tab-pane"
                                href="#basic-tab-pane"
                                role="tab"
                                aria-controls="#basic-tab-pane"
                                aria-selected="true"
                            >{{ __('people.profile.basic') }}</a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                id="contact-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#contact-tab-pane"
                                href="#contact-tab-pane"
                                role="tab"
                                aria-controls="#contact-tab-pane"
                                aria-selected="false"
                            >{{ __('people.profile.contact') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-2">
                <a type="button" class="btn btn-secondary profile-edit-btn" href="{{ route('people.edit', ['person' => $person->id]) }}">{{ __('people.profile.edit') }}</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="profile-work">
                    <p>Link Group 1</p>
                    <a href="">Link 1</a><br/>
                    <a href="">Link 2</a><br/>
                    <a href="">Link 3</a><br/>
                    <p>Link Group 2</p>
                    <a href="">Link 1</a><br/>
                    <a href="">Link 2</a><br/>
                    <a href="">Link 3</a><br/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="tab-content profile-tab" id="profile-tab-content">
                    <div class="tab-pane fade show active" id="basic-tab-pane" role="tabpanel" aria-labelledby="basic-tab" tabindex="0">
                        <ul class="list-group">
                            @foreach(['first', 'middle', 'last', 'nick', 'email', 'dob', 'ethnicity', 'title', 'suffix','honors','gender','pronouns','occupation','job_title','work_company','salutation','family_salutation'] as $field)
                                @if($person->$field)
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label>{{ __('people.profile.fields.' . $field) }}</label>
                                            <span>{{ ($field == 'dob')? $person->dob->format('m/d/Y'): $person->$field }}</span>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="1">
                        content here
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
