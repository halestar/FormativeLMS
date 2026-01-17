@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="row">
            {{-- Profile Image and Settings Column --}}
            <div class="col-md-4">
                <div class="d-flex flex-column">
                    {{-- Profile Image --}}
                    <div class="profile-img">
                        <img
                                class="img-fluid img-thumbnail"
                                @if($room->isFreeFloating())
                                    src="/images/free-floating-rooms.png"
                                @else
                                    src="{{ $room->building->img }}"
                                @endif
                                alt="{{ __('locations.buildings.img') }}"
                        />
                    </div>
                    {{-- Personal Settings and Links --}}
                    <div class="profile-work w-100">
                        <div class="alert text-bg-info mt-3 p-2">
                            <strong>{{ __('locations.rooms.capacity') }}:</strong> {{ $room->capacity }}
                        </div>
                        @can('locations.rooms')
                            @if($room->isPhysical())
                                <div class="mb-3">
                                    <a
                                            href="{{ route('locations.areas.show', $room->area_id) }}"
                                            class="btn btn-primary btn-lg d-flex justify-content-between align-items-center"
                                    >
                                        {{ __('locations.areas.bounds.define') }}
                                        <i class="fa-solid fa-arrow-up-right-from-square ps-2 ms-2 border-start"></i>
                                    </a>
                                </div>
                            @endif
                            @if($room->notes)
                                <div class="alert text-bg-primary">{{ $room->notes }}</div>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
            {{-- Main Content Column --}}
            <div class="col-md-8">
                <div class="row mb-4">
                    {{-- Basic Info --}}
                    <div class="col-md-8">
                        <div class="profile-head d-flex align-items-start flex-column h-100">
                            <h5>
                                {{ $room->name }}
                            </h5>
                            <h6 class="d-flex flex-column">
                                @if($room->isFreeFloating())
                                    <p class="lead mb-2">{{ trans_choice('locations.rooms.free', 1) }}</p>
                                @else
                                    <p class="lead mb-2">
                                        <a href="{{ route('locations.buildings.show', $room->building) }}">
                                            {{ $room->building->name }}
                                            ({{ $room->buildingArea->schoolArea->name }})
                                        </a>
                                    </p>
                                @endif
                                <div class="d-flex justify-content-start align-items-center">
                                    @foreach($room->campuses as $campus)
                                        {!! $campus->iconHtml("large") !!}
                                    @endforeach
                                </div>
                            </h6>
                        </div>
                    </div>
                    {{-- User Control --}}
                    <div class="col-md-4">
                        <div class="d-flex flex-column align-items-center">
                            @can('locations.rooms')
                                <a
                                        type="button"
                                        class="btn btn-secondary profile-edit-btn mb-2"
                                        href="{{ route('locations.rooms.edit', $room) }}"
                                >{{ __('locations.rooms.edit') }}</a>
                            @endcan
                        </div>
                    </div>
                </div>
                {{-- Profile Tabs --}}
                <ul class="nav nav-tabs mt-auto mb-4" id="profile-tab" role="tablist">
                    <li class="nav-item">
                        <a
                                class="nav-link active"
                                id="tab-basic"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-pane-basic"
                                href="#tab-pane-basic"
                                role="tab"
                                aria-controls="#tab-pane-basic"
                                aria-selected="true"
                                save-tab="basic"
                        >{{ __('people.profile.basic') }}</a>
                    </li>
                    <li class="nav-item">
                        <a
                                class="nav-link"
                                id="tab-schedule"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-pane-schedule"
                                href="#tab-pane-schedule"
                                role="tab"
                                aria-controls="#tab-pane-schedule"
                                aria-selected="false"
                                save-tab="schedule"
                        >{{ __('locations.rooms.schedule') }}</a>
                    </li>
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content profile-tab" id="profile-tab-content">
                    <div
                            class="tab-pane fade show active"
                            id="tab-pane-basic" role="tabpanel" aria-labelledby="tab-basic" tabindex="0"
                    >
                        <ul class="list-group">
                            @if($room->phone)
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-top">
                                        <label>
                                            {{ __('locations.rooms.phone') }}
                                        </label>
                                        <span>{{ $room->phone->pretty_phone }}</span>
                                    </div>
                                </li>
                            @endif
                            @if($room->isPhysical())
                                @if($room->building->address)
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-top">
                                            <label>
                                                {{ __('locations.buildings.address') }}
                                            </label>
                                            <span>{!! nl2br($room->building->address->pretty_address) !!}</span>
                                        </div>
                                    </li>
                                @endif
                                @if($room->building->phones()->count() > 0)
                                    @foreach($room->building->phones as $phone)
                                        <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                            <div class="d-flex justify-content-between align-items-top">
                                                <label>
                                                    {{ __('locations.buildings.phone') }}
                                                    (
                                                    @if($phone->personal->primary)
                                                        {{ __('phones.primary_phone') }}
                                                    @endif
                                                    {{ $phone->personal->label }}
                                                    )
                                                </label>
                                                <span>{!! nl2br($phone->pretty_phone) !!}</span>
                                            </div>
                                        </li>
                                    @endforeach

                                @endif
                            @endif
                        </ul>
                        @if($room->isPhysical())
                            <div id="blueprint-container"></div>
                        @endif
                    </div>
                    <div
                            class="tab-pane fade"
                            id="tab-pane-schedule" role="tabpanel" aria-labelledby="tab-schedule" tabindex="0"
                    >
                        <x-schedule-viewer :schedule-sources="$room->currentClassSessions()" :width="700"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        @if($room->isPhysical())
        var roomMap = new MapDrawings('blueprint-container', {{ $room->area_id }}, {highlightRoom: {{ $room->id }}});
        @endif
    </script>
@endpush
