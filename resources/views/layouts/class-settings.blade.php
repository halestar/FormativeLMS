@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="row g-0">
            <div class="col-md-4"><h3>{{ trans_choice('subjects.class', 2) }}</h3>
            </div>
            <div class="col-md-8">
                <ul class="nav nav-tabs">
                    <li class="nav-item"
                    >
                        <a
                                class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() != 'learning.classes.settings') border @endif"
                                style="background-color: {{ $classSelected->subject->color }}; color: {{ $classSelected->subject->getTextHex() }};border-color: {{ $classSelected->subject->color }};"
                                href="{{ route('learning.classes.settings', $classSelected->sessions()->first()) }}"
                        >
                            {{ __('system.menu.classes') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a
                                class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() != 'learning.classes.criteria') border @endif"
                                style="background-color: {{ $classSelected->subject->color }}; color: {{ $classSelected->subject->getTextHex() }};border-color: {{ $classSelected->subject->color }};"
                                href="{{ route('learning.classes.criteria', $classSelected->sessions()->first()) }}"
                        >
                            {{ __('system.menu.criteria') }}
                        </a>
                    </li>
                    @if($classSelected->currentSession()->class_management_id && $classSelected->currentSession()->classManager?->hasPreferences())
                    <li class="nav-item">
                        <a
                                class="nav-link @if(!str_starts_with(\Illuminate\Support\Facades\Route::currentRouteName(), 'integrators')) border @endif"
                                style="background-color: {{ $classSelected->subject->color }}; color: {{ $classSelected->subject->getTextHex() }};border-color: {{ $classSelected->subject->color }};"
                                href="{{ $classSelected->currentSession()->classManager->preferencesRoute($classSelected) }}"
                        >
                            {{ __('school.classes.management') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="row g-0">
            <div class="col-md-4">
                <div class="list-group list-group-flush">
                    @foreach($schoolClasses as $schoolClass)
                        <a
                                class="list-group-item list-group-item-action @if($schoolClass->id == $classSelected->id) active border-end-0 @else border-end border-end-3 @endif"
                                style="background-color: {{ $schoolClass->subject->color }}; color: {{ $schoolClass->subject->getTextHex() }}"
                                href="{{ route(\Illuminate\Support\Facades\Route::currentRouteName(), $schoolClass->currentSession()) }}"
                        >
                            @if($schoolClass->id == $classSelected->id)<i class="fa-solid fa-caret-right me-3"></i>@endif
                            {{ $schoolClass->sessions()->first()?->nameWithSchedule ?? "???" }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="col-md-8"
                 @if($classSelected)
                     style="background-color: {{ $classSelected->subject->color }}; color: {{ $classSelected->subject->getTextHex() }};"
                @endif
            >
                <div class="m-3 p-3" id="myTabContent">
                    @yield('class_settings_content')
                </div>
            </div>
        </div>
    </div>
@endsection