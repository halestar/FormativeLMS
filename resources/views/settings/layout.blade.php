@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <h4 class="card-header bg-primary-subtle d-flex justify-content-between align-items-center">
                <span class="card-title">{{ $title }}</span>
                <div class="settings-controls">
                    @foreach($buttons as $button)
                        <a href="{{ $button['url'] }}" class="{{ $button['classes'] }}" role="button">{{ $button['text'] }}</a>
                    @endforeach
                </div>
            </h4>
            <div class="card-body">
                @yield('settings_content')
            </div>
        </div>
    </div>
@endsection
