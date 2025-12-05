@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a
                        class="nav-link active"
                        id="tab-auth"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-pane-auth"
                        href="#tab-pane-auth"
                        role="tab"
                        aria-controls="tab-pane-auth"
                        aria-selected="true"
                        save-tab="auth"
                >{{ __('auth.auth') }}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a
                        class="nav-link"
                        id="tab-communications"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-pane-communications"
                        href="#tab-pane-communications"
                        role="tab"
                        aria-controls="tab-pane-communications"
                        aria-selected="false"
                        save-tab="communications"
                >{{ __('settings.communications') }}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a
                        class="nav-link"
                        id="tab-classes"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-pane-classes"
                        href="#tab-pane-classes"
                        role="tab"
                        aria-controls="tab-pane-classes"
                        aria-selected="false"
                        save-tab="classes"
                >{{ __('system.settings.tabs.learning') }}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a
                        class="nav-link"
                        id="tab-school"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-pane-school"
                        href="#tab-pane-school"
                        role="tab"
                        aria-controls="tab-pane-school"
                        aria-selected="false"
                        save-tab="school"
                >{{ __('system.settings.tabs.school') }}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a
                        class="nav-link"
                        id="tab-id"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-pane-id"
                        href="#tab-pane-id"
                        role="tab"
                        aria-controls="tab-pane-id"
                        aria-selected="false"
                        save-tab="id"
                >{{ trans_choice('people.id', 2) }}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a
                        class="nav-link"
                        id="tab-storage"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-pane-storage"
                        href="#tab-pane-storage"
                        role="tab"
                        aria-controls="tab-pane-storage"
                        aria-selected="false"
                        save-tab="storage"
                >{{ __('settings.storage.work') }}</a>
            </li>
        </ul>
        <div class="tab-content mt-2">
            <div class="tab-pane fade " id="tab-pane-school" role="tabpanel" aria-labelledby="tab-school"
                 tabindex="0">
                @include('settings.school.global')
            </div>
            <div class="tab-pane fade" id="tab-pane-classes" role="tabpanel" aria-labelledby="tab-classes" tabindex="1">
                @include('settings.school.classes')
            </div>
            <div class="tab-content mt-2">
                <div class="tab-pane fade " id="tab-pane-id" role="tabpanel" aria-labelledby="tab-id"
                     tabindex="0">
                    @include('settings.school.id')
                </div>
            </div>
            <div class="tab-content mt-2">
                <div class="tab-pane fade show active" id="tab-pane-auth" role="tabpanel" aria-labelledby="tab-auth"
                     tabindex="0">
                    @include('settings.school.auth')
                </div>
            </div>
            <div class="tab-content mt-2">
                <div class="tab-pane fade " id="tab-pane-storage" role="tabpanel" aria-labelledby="tab-storage"
                     tabindex="0">
                    @include('settings.school.storage')
                </div>
            </div>
            <div class="tab-content mt-2">
                <div class="tab-pane fade " id="tab-pane-communications" role="tabpanel" aria-labelledby="tab-communications"
                     tabindex="0">
                    @include('settings.school.communications')
                </div>
            </div>
        </div>
    </div>
@endsection
