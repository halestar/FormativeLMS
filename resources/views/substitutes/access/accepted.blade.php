@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <div class="text-center mb-4">
                    <h1 class="h3 mb-1">{{ __('features.substitutes.request.access.accepted.title') }}</h1>
                    <p class="text-muted mb-0">{{ __('features.substitutes.request.access.accepted.description') }}</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success fs-1"></i>
                        </div>
                        <h2 class="h5 mb-2">{{ __('features.substitutes.request.access.accepted.greeting', ['name' => $sub->name]) }}</h2>
                        <p class="text-muted mb-4">{{ __('features.substitutes.request.access.accepted.received') }}</p>

                        <div class="card border rounded-3 bg-body-tertiary shadow-sm mb-4 text-start">
                            <div class="card-body">
                                <div class="fw-semibold mb-1">
                                    {{ __('features.substitutes.request.access.accepted.summary', [
                                        'requester' => $subRequest->requester->name ?? $subRequest->requester_name,
                                        'date' => $subRequest->requested_for->format('m/d/Y'),
                                    ]) }}
                                </div>
                                <div class="small text-muted">
                                    {{ __('features.substitutes.request.access.time_window', [
                                        'start' => $subRequest->startTime()->format('h:i A'),
                                        'end' => $subRequest->endTime()->format('h:i A'),
                                    ]) }}
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success mb-0 text-start" role="alert">
                            {{ __('features.substitutes.request.access.accepted.notice') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
