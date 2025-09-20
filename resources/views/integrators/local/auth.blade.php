@extends('layouts.integrations', ['breadcrumb' => $breadcrumb, 'selectedService' => $authService])

@section('integrator-content')
    <form action="{{ route('integrators.local.auth.update') }}" method="POST">
        @csrf
        @method('PATCH')
        <h4>{{ __('integrators.local.auth.settings') }}</h4>
        <div class="mb-3">
            <label for="maxAttempts" class="form-label">{{ __('integrators.local.auth.maxAttempts') }}</label>
            <input
                    type="number"
                    class="form-control"
                    id="maxAttempts"
                    name="maxAttempts"
                    value="{{ $authService->data->maxAttempts }}"
                    required
                    aria-describedby="maxAttempts_help"
            />
            <div id="maxAttempts_help"
                 class="form-text">{!! __('integrators.local.auth.maxAttempts.description') !!}</div>
        </div>
        <div class="mb-3">
            <label for="decayMinutes" class="form-label">{{ __('integrators.local.auth.decayMinutes') }}</label>
            <input
                    type="number"
                    class="form-control"
                    id="decayMinutes"
                    name="decayMinutes"
                    value="{{ $authService->data->decayMinutes }}"
                    required
                    aria-describedby="decayMinutes_help"
            />
            <div id="decayMinutes_help"
                 class="form-text">{!! __('integrators.local.auth.decayMinutes.description') !!}</div>
        </div>
        <div class="mb-3">
            <label for="lockoutTimeout" class="form-label">{{ __('integrators.local.auth.lockoutTimeout') }}</label>
            <input
                    type="number"
                    class="form-control"
                    id="lockoutTimeout"
                    name="lockoutTimeout"
                    value="{{ $authService->data->lockoutTimeout }}"
                    required
                    aria-describedby="lockoutTimeout_help"
            />
            <div id="lockoutTimeout_help"
                 class="form-text">{!! __('integrators.local.auth.lockoutTimeout.description') !!}</div>
        </div>
        <div class="row row-cols">
            <button type="submit" class="btn btn-primary">{{ __('integrators.service.update') }}</button>
        </div>
    </form>
@endsection