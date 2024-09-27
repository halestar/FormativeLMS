<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

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
    @stack('head_scripts')
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
                                    @can('people.view.policies')
                                        <a class="dropdown-item" href="{{ route('people.policies.view.index') }}">
                                            {{ __('system.menu.view_policies') }}
                                        </a>
                                    @endcan
                                </div>
                            </li>
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
                    <div>
                        <button type="button" class="btn btn-success rounded rounded-5" data-bs-toggle="modal" data-bs-target="#search-modal"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                    @else
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif
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
                            <strong>Whoops! Something went wrong!</strong>
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
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            @session('success-status')
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-primary-subtle">
                    <img src="/images/fablms-32.png" class="rounded me-2" alt="fablms-logo">
                    <strong class="me-auto">{{ __('common.success') }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ $value }}
                </div>
            </div>
            @endsession
        </div>
    </div>
    <div class="modal fade" id="search-modal" tabindex="-1" aria-labelledby="#search" aria-hidden="true">
            <livewire:search />
    </div>
    <script>
        $('#search-modal').on('shown.bs.modal', function(){ $('#search').focus() })
    </script>
    @stack('scripts')
</body>
</html>
