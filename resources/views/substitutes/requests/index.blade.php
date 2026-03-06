@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div
                class="py-4"
                x-data="{
            per_page: {{ $person->getPreference('items_per_page', 25) }},
            async updatePerPage() {
                setPersonalPreference('items_per_page', this.per_page)
            }
        }"
        >
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div class="flex-grow-1">
                    <h1 class="h3 mb-1">{{ __('features.substitutes.requests') }}</h1>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('features.substitutes.pool.index') }}" class="btn btn-primary">
                        {{ __('features.substitutes.pool.manage') }}
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('features.substitutes.index') }}"
                          class="row g-2 align-items-end">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <div class="col-12 col-md-4 col-lg-3">
                            <label for="requested_for" class="form-label mb-1 text-muted small">{{ __('pagination.filter.date') }}</label>
                            <input
                                    id="requested_for"
                                    type="date"
                                    name="requested_for"
                                    class="form-control"
                                    value="{{ $requestedFor }}"
                            />
                        </div>
                        <div class="col-12 col-md-auto d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('common.apply') }}</button>
                            <a href="{{ route('features.substitutes.index', ['tab' => $tab]) }}"
                               class="btn btn-outline-secondary">{{ __('common.clear') }}</a>
                        </div>
                        <div class="col-12 col-md-auto ms-md-auto">
                            <label for="per-page" class="form-label mb-1 text-muted small">{{ __('pagination.per_page.entries') }}</label>
                            <select
                                    id="per-page"
                                    class="form-select"
                                    style="width: auto;"
                                    x-model="per_page"
                                    @change="updatePerPage"
                            >
                                @foreach ([10, 25, 50, 100] as $size)
                                    <option value="{{ $size }}">{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <a
                            class="nav-link {{ $tab === 'incomplete' ? 'active' : '' }}"
                            href="{{ route('features.substitutes.index', array_merge(request()->except(['tab', 'incomplete_page', 'upcoming_page', 'past_page']), ['tab' => 'incomplete'])) }}"
                    >
                        {{ __('common.incomplete') }}
                        <span class="badge text-bg-danger ms-1">{{ $incompleteRequests->total() }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a
                            class="nav-link {{ $tab === 'upcoming' ? 'active' : '' }}"
                            href="{{ route('features.substitutes.index', array_merge(request()->except(['tab', 'incomplete_page', 'upcoming_page', 'past_page']), ['tab' => 'upcoming'])) }}"
                    >
                        {{ __('common.upcoming') }}
                        <span class="badge text-bg-secondary ms-1">{{ $upcomingRequests->total() }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a
                            class="nav-link {{ $tab === 'past' ? 'active' : '' }}"
                            href="{{ route('features.substitutes.index', array_merge(request()->except(['tab', 'incomplete_page', 'upcoming_page', 'past_page']), ['tab' => 'past'])) }}"
                    >
                        {{ __('common.past') }}
                        <span class="badge text-bg-secondary ms-1">{{ $pastRequests->total() }}</span>
                    </a>
                </li>
            </ul>

            @php
                $tabConfig = [
                    'incomplete' => ['title' => 'Incomplete Requests', 'requests' => $incompleteRequests, 'page' => 'incomplete_page'],
                    'upcoming' => ['title' => 'Upcoming Completed Requests', 'requests' => $upcomingRequests, 'page' => 'upcoming_page'],
                    'past' => ['title' => 'Past Requests', 'requests' => $pastRequests, 'page' => 'past_page'],
                ];
            @endphp

            @foreach ($tabConfig as $key => $config)
                <div class="{{ $tab === $key ? '' : 'd-none' }}">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h2 class="h5 mb-3">{{ $config['title'] }}</h2>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>{{ __('features.substitutes.requester') }}</th>
                                        <th>{{ trans_choice('locations.campus', 2) }}</th>
                                        <th>{{ __('common.date') }}</th>
                                        <th>{{ trans_choice('subjects.class', 2) }}</th>
                                        <th>{{ trans_choice('features.substitutes', 1) }}</th>
                                        <th>{{ __('features.substitutes.response.date') }}</th>
                                        <th class="text-end"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($config['requests'] as $request)
                                        @php
                                            $assignedCampusRequests = $request->campusRequests->filter(fn($campusRequest) => !is_null($campusRequest->substitute_id));
                                            $rowClass = !$request->completed
                                                ? 'table-danger'
                                                : ($assignedCampusRequests->isNotEmpty() ? 'table-success' : 'table-warning');

                                            $requesterName = $request->requester?->name ?? $request->requester_name ?? '—';
                                            $campusNames = $request->campusRequests
                                                ->map(fn($campusRequest) => $campusRequest->campus?->name)
                                                ->filter()
                                                ->unique()
                                                ->values();
                                            $substituteNames = $assignedCampusRequests
                                                ->map(fn($campusRequest) => $campusRequest->substitute?->name)
                                                ->filter()
                                                ->unique()
                                                ->values();
                                            $responseDates = $assignedCampusRequests
                                                ->map(fn($campusRequest) => $campusRequest->responded_on ? \Illuminate\Support\Carbon::parse($campusRequest->responded_on)->format('m/d/Y h:i A') : null)
                                                ->filter()
                                                ->unique()
                                                ->values();
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td class="fw-semibold">{{ $requesterName }}</td>
                                            <td>{{ $campusNames->isNotEmpty() ? $campusNames->join(', ') : '—' }}</td>
                                            <td>{{ $request->requested_for?->format('m/d/Y') ?? '—' }}</td>
                                            <td>{{ $request->class_requests_count }}</td>
                                            <td>
                                                @if ($substituteNames->isNotEmpty())
                                                    {{ $substituteNames->join(', ') }}
                                                @elseif ($request->completed)
                                                    {{ __('features.substitutes.covered.internally') }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>{{ $responseDates->isNotEmpty() ? $responseDates->join(', ') : '—' }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('features.substitutes.show', $request) }}"
                                                   class="btn btn-sm btn-outline-primary">{{ __('common.view') }}</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                {{ __('features.substitutes.requests.no') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
                                <div class="text-muted small">
                                    @if ($config['requests']->total() > 0)
                                        {{ __('pagination.showing', ['first' => $config['requests']->firstItem(), 'last' => $config['requests']->lastItem(), 'total' => $config['requests']->total() ]) }}
                                    @else
                                    {{ __('pagination.no_results') }}
                                    @endif
                                </div>
                                <div>
                                    {{ $config['requests']->appends(['tab' => $key])->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
