<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="https://dev.kalinec.net/storage/cms/qwo7N9rHAFb0JiCJPvavXYzHn2Vz9T0F5ap8I4GV.ico"/>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Stylesheets -->
    @livewireStyles
    <link rel="stylesheet" href="{{ mix('css/app.css') }}"/>
    @stack('stylesheets')

    <!-- Scripts -->
    @livewireScripts
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="{{ mix('js/lms-tools.js') }}"></script>
    <script src="https://kit.fontawesome.com/d18ee59f88.js" crossorigin="anonymous"></script>
    @stack('head_scripts')
    @auth
        @include('layouts.echo')
    @endauth
</head>
<body>
<div id="app">
    @include('layouts.menu')
    @isset($breadcrumb)
        <div class="bg-primary-subtle rounded">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><span class="fa fa-home"></span></a></li>
                @foreach($breadcrumb as $crumb => $url)
                    @if($loop->last)
                        <li class="breadcrumb-item active" aria-current="page">{{ $crumb }}</li>
                    @else
                        <li class="breadcrumb-item"><a href="{{ $url }}">{{ $crumb }}</a></li>
                    @endif
                @endforeach
            </ol>
        </div>
    @endisset
    @if($errors->count() > 0)
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-danger">
                        <strong>{{ __('errors.whoops_something_went_wrong') }}</strong>
                        <br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <main class="py-4 position-relative">
        @yield('content')
    </main>
    <footer class="fixed-bottom bg-primary-subtle rounded text-center text-small">
        &copy; 2025 German Kalinec
        &nbsp; &#9679; &nbsp;
        {{ \App\Models\Locations\Year::currentYear()->label }}
        @auth
            @if(\Illuminate\Support\Facades\Auth::user()->isEmployee())
                @foreach(\Illuminate\Support\Facades\Auth::user()->campuses as $campus)
                    &nbsp; &#9679; &nbsp;
                    {{ $campus->abbr }}
                    {{ \App\Models\Locations\Term::currentTerm($campus)->label }}
                    {{ trans_choice('locations.terms', 1) }}
                @endforeach
            @elseif(\Illuminate\Support\Facades\Auth::user()->isStudent())
                &nbsp; &#9679; &nbsp;
                {{ \Illuminate\Support\Facades\Auth::user()->student()->campus->abbr }}
                {{ \App\Models\Locations\Term::currentTerm(\Illuminate\Support\Facades\Auth::user()->student()->campus)->label }}
                {{ trans_choice('locations.terms', 1) }}
            @elseif(\Illuminate\Support\Facades\Auth::user()->isParent() && \Illuminate\Support\Facades\Auth::user()->viewingStudent)
                &nbsp; &#9679; &nbsp;
                {{ \Illuminate\Support\Facades\Auth::user()->viewingStudent->campus->abbr }}
                {{ \App\Models\Locations\Term::currentTerm(\Illuminate\Support\Facades\Auth::user()->viewingStudent->campus)->label }}
                {{ trans_choice('locations.terms', 1) }}
            @endif
        @endauth
    </footer>
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container">
        @session('success-status')
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" id="success-toast">
            <div class="toast-header bg-primary-subtle">
                <img src="/images/fablms-32.png" class="rounded me-2" alt="fablms-logo">
                <strong class="me-auto">{{ __('common.success') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"
                        aria-label="{{ trans('common.close') }}"></button>
            </div>
            <div class="toast-body">
                {{ $value }}
            </div>
        </div>
        <script>
            $(document).ready(function () {
                setTimeout(function () {
                    $('#success-toast').hide();
                }, 3000)
            })
        </script>
        @endsession
        <template id="toast-template">
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <span class="toast-icon me-3"></span>
                    <strong class="me-auto toast-title"></strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"
                            aria-label="{{ trans('common.close') }}"></button>
                </div>
                <div class="toast-body"></div>
            </div>
        </template>
    </div>
</div>
@auth
    <div class="modal fade" id="search-modal" tabindex="-1" aria-labelledby="#search" aria-hidden="true"
         data-bs-keyboard="false">
        <livewire:search/>
    </div>
    <script>
        $('#search-modal').on('shown.bs.modal', function () {
            $('#search').focus()
        });
        window.sessionSettings = new SessionSettings('{{ Route::currentRouteName() }}');
    </script>
    <div class="modal fade" id="document-browser-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <livewire:storage.document-storage-browser/>
    </div>
@endauth
<template id="notification-template">
    <a class="dropdown-item notification" href="#">
        <div class="notification-header d-flex justify-content-between align-items-center">
            <strong class="notification-title"></strong>
            <span class="notification-icon"></span>
        </div>
        <div class="notification-body"></div>
    </a>
</template>
@stack('scripts')
</body>
</html>
