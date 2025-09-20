@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        @if($person->authConnection->mustChangePassword())
            <div class="alert alert-warning mb-3">
                {{ __('passwords.change.must') }}
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('settings.auth.password.change') }}</h3>
            </div>
            <div class="card-body">
                <livewire:auth.change-password-form :person="$person"/>
            </div>
        </div>
    </div>
@endsection