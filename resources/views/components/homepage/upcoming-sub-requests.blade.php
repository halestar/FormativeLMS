<div class="card shadow-sm border-0 m-2" style="max-height: 350px; display: flex; flex-direction: column;">
    <div class="card-header bg-white border-bottom pt-3 pb-2 sticky-top z-1">
        <h5 class="mb-0 text-primary fw-bold">
            <i class="fa-solid fa-calendar-day me-2"></i>{{ __('features.substitutes.requests.upcoming') }}
        </h5>
    </div>
    <div class="card-body p-0 overflow-auto" style="flex-grow: 1;">
        <ul class="list-group list-group-flush">
            @foreach ($subRequests as $subRequest)
                <li class="list-group-item list-group-item-action border-start-0 border-end-0 py-3">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 fw-semibold text-dark">
                                {{ \Carbon\Carbon::parse($subRequest->requested_for)->format('l, F j, Y') }}
                            </h6>
                            <div class="small mt-1">
                                @if($subRequest->internal)
                                    <span class="text-info"><i
                                                class="fa-solid fa-building me-1"></i>{{ __('features.substitutes.requests.coverage.internally') }}</span>
                                @elseif($subRequest->isCompleted())
                                    <span class="text-secondary"><i class="fa-solid fa-user-group me-1"></i>{{ __('features.substitutes.signed') }}</span>
                                @else
                                    <span class="text-secondary"><i class="fa-solid fa-user-group me-1"></i>{{ __('common.pending') }}</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            @if($subRequest->completed)
                                <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm"><i
                                            class="fa-solid fa-check me-1"></i>{{ __('features.substitutes.covered') }}</span>
                            @else
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2 shadow-sm"><i
                                            class="fa-solid fa-hourglass-half me-1"></i>{{ __('common.pending') }}</span>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>