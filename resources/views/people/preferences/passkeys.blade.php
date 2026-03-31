@extends('layouts.app', ['breadcrumb' => $breadcrumb])
@inject('authSettings', 'App\\Classes\\Settings\\AuthSettings')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                @if($authSettings->passkeys_require && $person->passkeys->isEmpty())
                    <div class="alert alert-warning shadow-sm" role="alert">
                        {!! __('auth.passkey.required') !!}
                    </div>
                @endif

                <livewire:passkeys/>
            </div>
        </div>
    </div>
@endsection