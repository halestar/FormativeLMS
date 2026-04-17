@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <div class="text-center mb-4">
                    <h1 class="h3 mb-1">{{ __('features.substitutes.request.access.title') }}</h1>
                    <p class="text-muted mb-0">{{ __('features.substitutes.request.access.description') }}</p>
                </div>

                <form action="{{ route('subs.request.accept', ['token' => $token]) }}" method="POST">
                    @csrf

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <h2 class="h5 mb-2">{{ __('features.substitutes.request.access.greeting', ['name' => $sub->name]) }}</h2>
                            <p class="text-muted mb-4">{{ __('features.substitutes.request.access.intro') }}</p>

                            <div class="card border rounded-3 bg-body-tertiary shadow-sm mb-4">
                                <div class="card-body">
                                    <div class="fw-semibold mb-1">
                                        {{ __('features.substitutes.request.access.summary', [
                                            'requester' => $subRequest->requester_name,
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

                            <div class="mb-4">
                                <div class="mb-3">
                                    <h3 class="h5 mb-1">{{ __('features.substitutes.request.access.classes.heading') }}</h3>
                                    <p class="text-muted small mb-0">{{ __('features.substitutes.request.access.classes.description') }}</p>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th>{{ trans_choice('subjects.class', 1) }}</th>
                                            <th>{{ __('features.substitutes.request.access.classes.room') }}</th>
                                            <th>{{ __('features.substitutes.request.create.classes.start_time') }}</th>
                                            <th>{{ __('features.substitutes.request.create.classes.end_time') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($classRequests as $section)
                                            <tr>
                                                <td class="fw-semibold">{{ $section->session->name }}</td>
                                                <td>{{ $section->session->room->name }}</td>
                                                <td>{{ $section->start_on->format('h:i A') }}</td>
                                                <td>{{ $section->end_on->format('h:i A') }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <input type="hidden" name="token" value="{{ $token }}" />

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">{{ __('features.substitutes.request.access.actions.accept') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
