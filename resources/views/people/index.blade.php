@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex flex-column flex-lg-row gap-2 justify-content-between align-items-lg-center mb-3">
                    <div>
                        <h2 class="h4 mb-1">{{ __('people.school.directory') }}</h2>
                        <p class="text-body-secondary mb-0">Browse your directory with quick filters and compact person cards.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <form method="GET" class="d-flex align-items-center gap-2 mb-0">
                            <label for="items_per_page" class="small text-body-secondary text-nowrap mb-0">Show</label>
                            <select
                                id="items_per_page"
                                name="items_per_page"
                                class="form-select form-select-sm"
                                onchange="this.form.submit()"
                            >
                                @foreach($itemsPerPageOptions as $option)
                                    <option value="{{ $option }}" @selected($itemsPerPage === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                            <span class="small text-body-secondary text-nowrap">per page</span>
                            <input type="hidden" name="role" value="{{ $selectedRole }}" />
                            <input type="hidden" name="campus" value="{{ $selectedCampus }}" />
                            <input type="hidden" name="search" value="{{ $search }}" />
                        </form>
                        @can('create', \App\Models\People\Person::class)
                            <a
                                href="{{ route('people.create') }}"
                                role="button"
                                class="btn btn-success"
                            >
                                {!! __('people.add_new_person') !!}
                            </a>
                        @endcan
                    </div>
                </div>

                <form method="GET" class="row g-2 align-items-end mb-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input
                            id="search"
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="Search by name or email"
                        />
                    </div>
                    <div class="col-md-3">
                        <label for="role" class="form-label">Role</label>
                        <select id="role" name="role" class="form-select">
                            @foreach($roleOptions as $roleValue => $roleLabel)
                                <option value="{{ $roleValue }}" @selected($selectedRole === $roleValue)>{{ $roleLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="campus" class="form-label">Campus</label>
                        <select id="campus" name="campus" class="form-select">
                            <option value="">All campuses</option>
                            @foreach($campusOptions as $campus)
                                <option value="{{ $campus->id }}" @selected($selectedCampus === $campus->id)>
                                    {{ $campus->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <input type="hidden" name="items_per_page" value="{{ $itemsPerPage }}" />
                        <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                        <a href="{{ route('people.index') }}" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>

                <div class="list-group list-group-flush border rounded overflow-hidden">
                    @forelse($people as $person)
                        @php
                            $studentRecord = $person->studentRecords->first();
                            $parentCampuses = $person->isParent() ? $person->parentCampuses() : collect();
                            $employeeCampuses = $person->employeeCampuses;
                            $substituteCampuses = $person->substituteProfile?->campuses ?? collect();
                            $employeeRole = $person->hasRole(\App\Models\Utilities\SchoolRoles::$FACULTY)
                                ? \App\Models\Utilities\SchoolRoles::$FACULTY
                                : ($person->hasRole(\App\Models\Utilities\SchoolRoles::$STAFF) ? \App\Models\Utilities\SchoolRoles::$STAFF : trans_choice('people.employee', 1));
                        @endphp
                        <a
                            href="{{ route('people.show', ['person' => $person->school_id]) }}"
                            class="list-group-item list-group-item-action px-3 py-2 text-reset text-decoration-none"
                        >
                            <div class="d-flex align-items-center gap-3 justify-content-between">
                                <img
                                    class="rounded-circle border object-fit-cover flex-shrink-0"
                                    src="{{ $person->portrait_url->thumbUrl() }}"
                                    alt="{{ __('people.profile.image') }}"
                                    style="width: 44px; height: 44px;"
                                />
                                <div class="flex-grow-1 min-w-0">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-lg-4 min-w-0">
                                            <div class="fw-semibold text-truncate lh-sm">{{ $person->name }}</div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="d-flex flex-wrap align-items-center gap-1 justify-content-start justify-content-lg-end small">
                                                @if($person->isStudent())
                                                    <span class="badge text-bg-primary">{{ __('common.student') }}</span>
                                                    @if($studentRecord?->level)
                                                        <span class="badge text-bg-light border text-dark">{{ $studentRecord->level->name }}</span>
                                                    @endif
                                                @endif
                                                @if($person->isParent())
                                                    <span class="badge text-bg-primary">{{ __('common.parent') }}</span>
                                                    @foreach($parentCampuses as $campus)
                                                        <span class="badge text-bg-light border text-dark">{{ $campus->abbr }}</span>
                                                    @endforeach
                                                @endif
                                                @if($person->isEmployee())
                                                    <span class="badge text-bg-primary">{{ trans_choice('people.employee', 1) }}</span>
                                                    <span class="badge text-bg-info">{{ $employeeRole }}</span>
                                                    @foreach($employeeCampuses as $campus)
                                                        <span class="badge text-bg-light border text-dark">{{ $campus->abbr }}</span>
                                                    @endforeach
                                                @endif
                                                @if($person->isSubstitute())
                                                    <span class="badge text-bg-primary">{{ \App\Models\Utilities\SchoolRoles::$SUBSTITUTE }}</span>
                                                    @foreach($substituteCampuses as $campus)
                                                        <span class="badge text-bg-light border text-dark">{{ $campus->abbr }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-1 d-flex justify-content-lg-end">
                                            <i class="fa fa-chevron-right text-body-secondary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-5 text-center text-body-secondary">
                            No people matched the current filters.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="mt-3">
            {{ $people->links() }}
        </div>
    </div>
@endsection
