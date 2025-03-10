<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="https://storage.googleapis.com/deep-citizen-425500-e0.appspot.com/cms/krTY3jXltMsvfKcCgM5ZB7rXegNfoB70hGgV3ce6.ico"/>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <script src="https://kit.fontawesome.com/d18ee59f88.js" crossorigin="anonymous"></script>


    <!-- Stylesheets -->
    @livewireStyles
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" />
    @stack('stylesheets')

    <!-- Scripts -->
    @livewireScripts
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="{{ mix('js/lms-tools.js') }}"></script>
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    @stack('head_scripts')
    @auth
        @include('layouts.echo')
    @endauth
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md bg-primary" data-bs-theme="dark">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('people.index') }}" >
                                    {{ __('system.menu.school.directory') }}
                                </a>
                            </li>
                            @canany(['locations.campuses', 'locations.years', 'locations.buildings', 'subjects.subjects', 'subjects.courses', 'subjects.classes'])
                            <li class="nav-item dropdown">
                                <a id="schoolAdminDD" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ __('system.menu.school.administration') }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="schoolAdminDD">
                                    @can('locations.campuses')
                                        <a class="dropdown-item" href="{{ route('locations.campuses.index') }}">
                                            {{ __('system.menu.campuses') }}
                                        </a>
                                    @endcan
                                    @can('locations.years')
                                        <a class="dropdown-item" href="{{ route('locations.years.index') }}">
                                            {{ __('system.menu.years') }}
                                        </a>
                                    @endcan
                                    @can('locations.buildings')
                                        <a class="dropdown-item" href="{{ route('locations.buildings.index') }}">
                                            {{ __('system.menu.rooms') }}
                                        </a>
                                    @endcan
                                    @can('subjects.subjects')
                                        <a class="dropdown-item" href="{{ route('subjects.subjects.index') }}">
                                            {{ trans_choice('subjects.subject',2) }}
                                        </a>
                                    @endcan
                                    @can('subjects.courses')
                                        <a class="dropdown-item" href="{{ route('subjects.courses.index') }}">
                                            {{ trans_choice('subjects.course',2) }}
                                        </a>
                                    @endcan
                                    @can('subjects.classes')
                                        <a class="dropdown-item" href="{{ route('subjects.classes.index') }}">
                                            {{ trans_choice('subjects.class',2) }}
                                        </a>
                                    @endcan
                                </div>
                            </li>
                            @endcanany
                            @canany(['classes.enrollment'])
                            <li class="nav-item dropdown">
                                <a id="classManagementDD" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ __('system.menu.classes') }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="classManagementDD">
                                    @can('classes.enrollment')
                                        <a class="dropdown-item" href="{{ route('subjects.enrollment.general') }}">
                                            {{ __('system.menu.classes.enrollment.general') }}
                                        </a>
                                    @endcan
                                </div>
                            </li>
                            @endcanany
                            @canany(['crud', 'cms', 'people.roles.fields', 'people.field.permissions'])
                            <li class="nav-item dropdown">
                                <a id="adminDD" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ __('system.menu.admin') }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDD">
                                    @can('crud')
                                    <a class="dropdown-item" href="{{ route('crud') }}">
                                        {{ __('system.menu.crud') }}
                                    </a>
                                    @endcan
                                    @can('cms')
                                    <a class="dropdown-item" href="/cms">
                                        {{ __('system.menu.cms') }}
                                    </a>
                                    @endcan
                                    @can('people.roles.fields')
                                        <a class="dropdown-item" href="{{ route('people.roles.fields') }}">
                                            {{ __('people.fields.roles') }}
                                        </a>
                                    @endcan
                                    @can('people.field.permissions')
                                        <a class="dropdown-item" href="{{ route('people.fields.permissions') }}">
                                            {{ __('system.menu.fields') }}
                                        </a>
                                    @endcan
                                    @can('school')
                                        <a class="dropdown-item" href="{{ route('school.settings') }}">
                                            {{ __('system.menu.school.settings') }}
                                        </a>
                                    @endcan
                                </div>
                            </li>
                            @endcanany
                            <li class="nav-item dropdown">
                                <a id="userDD" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDD">
                                    <a class="dropdown-item" href="{{ route('people.show', ['person' => Auth::user()->id]) }}">
                                        {{ __('people.profile.mine') }}
                                    </a>
                                    @can('settings.permissions.view')
                                    <a class="dropdown-item" href="{{ route('settings.permissions.index') }}">
                                        {{ __('settings.permissions') }}
                                    </a>
                                    @endcan
                                    @can('settings.roles.view')
                                    <a class="dropdown-item" href="{{ route('settings.roles.index') }}">
                                        {{ __('settings.roles') }}
                                    </a>
                                    @endcan
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endauth
                    </ul>
                    @auth
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <button type="button" class="btn btn-success rounded rounded-5" data-bs-toggle="modal" data-bs-target="#search-modal"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </li>
                            <li class="nav-item dropdown ms-3  @if(Auth::user()->unreadNotifications()->count() == 0) d-none @endif" id="notification-menu">
                                <a id="user-notifications" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fa-solid fa-bell fs-4 text-bright-alert glowing"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="user-notifications" id="notifications-dropdown-container">
                                    @foreach(Auth::user()->unreadNotifications as $notification)
                                        <x-notification :notification="$notification" />
                                    @endforeach
                                </div>
                            </li>
                        </ul>
                    @endauth
                </div>
            </div>
        </nav>
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
        <main class="py-4">
            @yield('content')
        </main>
        <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container">
            @session('success-status')
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" id="success-toast">
                <div class="toast-header bg-primary-subtle">
                    <img src="/images/fablms-32.png" class="rounded me-2" alt="fablms-logo">
                    <strong class="me-auto">{{ __('common.success') }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="{{ trans('common.close') }}"></button>
                </div>
                <div class="toast-body">
                    {{ $value }}
                </div>
            </div>
            <script>
                $(document).ready(function()
                {
                    setTimeout(function()
                    {
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
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="{{ trans('common.close') }}"></button>
                    </div>
                    <div class="toast-body"></div>
                </div>
            </template>
        </div>
    </div>
    <div class="modal fade" id="search-modal" tabindex="-1" aria-labelledby="#search" aria-hidden="true">
            <livewire:search />
    </div>
    <template id="notification-template">
        <a class="dropdown-item notification" href="#">
            <div class="notification-header d-flex justify-content-between align-items-center">
                <strong class="notification-title"></strong>
                <span class="notification-icon"></span>
            </div>
            <div class="notification-body"></div>
        </a>
    </template>
    <script>
        $('#search-modal').on('shown.bs.modal', function(){ $('#search').focus() })
        @auth
            window.sessionSettings = new SessionSettings('{{ Route::currentRouteName() }}');
        @endauth
    </script>
    @stack('scripts')
</body>
</html>
