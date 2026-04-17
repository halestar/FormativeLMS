@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="py-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div class="flex-grow-1">
                    <h1 class="h3 mb-1">{{ __('features.substitutes.pool') }}</h1>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('features.substitutes.pool.create') }}" class="btn btn-primary">{{ __('features.substitutes.pool.new') }}</a>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="GET" action="{{ route('features.substitutes.pool.index') }}"
                  class="d-flex justify-content-between gap-2 mb-3">
                <div class="input-group" style="max-width: 360px;">
                    <input
                            id="sub-search"
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="{{ __('features.substitutes.filter') }}..."
                            value="{{ $search }}"
                    />
                    <button class="btn btn-primary" type="submit" aria-label="{{ __('features.substitutes.filter') }}">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('features.substitutes.pool.index') }}" class="btn btn-secondary">{{ __('common.clear') }}</a>
                </div>
                <div class="form-check form-switch ms-1">
                    <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            value="1"
                            id="show-inactive"
                            name="show_inactive"
                            @checked($showInactive)
                    >
                    <label class="form-check-label" for="show-inactive">
                        {{ __('common.inactive.show') }}
                    </label>
                </div>
            </form>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse ($subs as $substitute)
                            <div class="list-group-item list-group-item-action px-3 py-2 {{ !$substitute->active && $showInactive ? 'list-group-item-warning' : '' }}">
                                <div class="d-flex flex-column flex-xl-row align-items-start align-items-xl-center gap-2 gap-xl-3">
                                    <div class="flex-shrink-0" style="min-width: 260px;">
                                        <div class="fw-semibold">
                                            {{ $substitute->person?->name ?? 'Unknown Person' }}
                                            @if (!$substitute->active && $showInactive)
                                                <span class="badge text-bg-warning ms-2">{{ __('common.inactive') }}</span>
                                            @endif
                                        </div>
                                        <div class="text-muted small">{{ $substitute->person?->email ?? 'No email address' }}</div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-1 flex-grow-1">
                                        @forelse ($substitute->campuses as $campus)
                                            <span class="badge rounded-pill bg-light text-dark border">{{ $campus->name }}</span>
                                        @empty
                                            <span class="text-muted small">{{ __('locations.campus.no') }}</span>
                                        @endforelse
                                    </div>

                                    <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                    <span class="small">
                                        <i class="bi bi-envelope-fill {{ $substitute->email_confirmed ? 'text-success' : 'text-danger' }}"></i>
                                        <span class="ms-1 d-none d-sm-inline">{{ __('people.profile.fields.email') }}</span>
                                    </span>
                                        <span class="small">
                                        <i class="bi bi-chat-dots-fill {{ $substitute->sms_confirmed ? 'text-success' : 'text-danger' }}"></i>
                                        <span class="ms-1 d-none d-sm-inline">{{ __('settings.communications.sms') }}</span>
                                    </span>
                                    </div>

                                    <div class="ms-xl-auto">
                                        <a href="{{ route('features.substitutes.pool.show', $substitute->person->school_id) }}"
                                           class="btn btn-sm btn-outline-primary">{{ __('common.view') }}</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                {{ __('features.substitutes.no') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
