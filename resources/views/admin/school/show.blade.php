@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a
                        class="nav-link"
                        id="tab-school"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-pane-school"
                        href="#tab-pane-school"
                        role="tab"
                        aria-controls="tab-pane-school"
                        aria-selected="true"
                        save-tab="school"
                >{{ __('system.settings.tabs.school') }}</a>
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
                >{{ __('system.settings.tabs.classes') }}</a>
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
                        id="tab-auth"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-pane-auth"
                        href="#tab-pane-auth"
                        role="tab"
                        aria-controls="tab-pane-auth"
                        aria-selected="false"
                        save-tab="auth"
                >{{ __('auth.auth') }}</a>
            </li>
        </ul>
        <div class="tab-content mt-2">
            <div class="tab-pane fade show active" id="tab-pane-school" role="tabpanel" aria-labelledby="tab-school"
                 tabindex="0">
                @include('admin.school.global')
            </div>
            <div class="tab-pane fade" id="tab-pane-classes" role="tabpanel" aria-labelledby="tab-classes" tabindex="1">
                @include('admin.school.classes')
            </div>
            <div class="tab-content mt-2">
                <div class="tab-pane fade " id="tab-pane-id" role="tabpanel" aria-labelledby="tab-id"
                     tabindex="0">
                    @include('admin.school.id')
                </div>
            </div>
            <div class="tab-content mt-2">
                <div class="tab-pane fade " id="tab-pane-auth" role="tabpanel" aria-labelledby="tab-auth"
                     tabindex="0">
                    @include('admin.school.auth')
                </div>
            </div>
        </div>
    </div>
@endsection
