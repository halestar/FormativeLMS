@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('settings.auth.password.change') }}</h3>
            </div>
            <div class="card-body">
                <livewire:auth.change-password-form :person="$person" />
            </div>
        </div>
    </div>
@endsection