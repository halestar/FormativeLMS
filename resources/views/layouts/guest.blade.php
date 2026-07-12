<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') . (isset($breadcrumb)? ": " . array_key_last($breadcrumb): '') }}</title>
    <link rel="icon" href="/favicon.ico"/>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Stylesheets -->
    @livewireStyles
    <link rel="stylesheet" href="{{ mix('css/app.css') }}"/>
    @stack('stylesheets')

    <!-- Scripts -->
    @livewireScripts
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="{{ mix('js/lms-tools.js') }}"></script>
    <script src="https://kit.fontawesome.com/d18ee59f88.js" crossorigin="anonymous"></script>
    @stack('head_scripts')
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md bg-primary" data-bs-theme="dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/fablms-512.png', true) }}" alt="FABLMS" width="32" height="32">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item  my-auto">
                        <x-utilities.language-switcher/>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="py-4 position-relative">
        @isset($livewireFullPage)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>
    <footer class="fixed-bottom bg-primary-subtle rounded text-center text-small">
        &copy; 2026 German Kalinec
    </footer>
</div>
@stack('scripts')
</body>
</html>
