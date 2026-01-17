@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="row">
            {{-- Campus Image and Settings Column --}}
            <div class="col-md-4">
                <div class="d-flex flex-column">
                    {{-- Campus Image --}}
                    <div class="profile-img">
                        <img
                                class="img-fluid img-thumbnail"
                                src="{{ $building->img }}"
                                alt="{{ __('locations.buildings.img') }}"
                        />
                    </div>
                    {{-- Side Menu --}}
                    <div class="profile-work">
                        @if($building->buildingAreas->count() > 0)
                        <p>{{ trans_choice('locations.buildings.areas', $building->buildingAreas()->count()) }}</p>
                        <ul
                            class="list-group"
                            x-data="
                            {
                                area_id: {{ $building->buildingAreas->first()->id }},
                                gotoRoom(room_id)
                                {
                                    window.location.href = '/locations/rooms/' + room_id;
                                },
                                openArea()
                                {
                                    $('.room-display[area-id]').addClass('d-none');
                                    $('#blueprint-container').empty();
                                    $('.room-display[area-id=' + this.area_id + ']').removeClass('d-none');
                                    new MapDrawings('blueprint-container', this.area_id, { action: this.gotoRoom });
                                }
                            }"
                            x-init="openArea()"
                        >
                            @foreach($building->buildingAreas as $area)
                                <li
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center show-as-action"
                                    :class="(area_id == {{ $area->id }}) ? 'active' : ''"
                                    @click="area_id = {{ $area->id }}; openArea()"
                                >
                                    {{ $area->schoolArea->name }}
                                </li>
                            @endforeach
                        </ul>
                        @endif
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
                                {{ $building->name }}
                            </h5>
                        </div>
                    </div>
                    {{-- User Control --}}
                    <div class="col-md-4">
                        @can('locations.buildings')
                        <div class="d-flex flex-column align-items-center">
                            <a
                                    type="button"
                                    class="btn btn-secondary profile-edit-btn"
                                    href="{{ route('locations.buildings.edit', ['building' => $building->id]) }}"
                            >{{ __('locations.buildings.edit') }}</a>
                        </div>
                        @endcan
                    </div>
                </div>
                {{-- Profile Tabs --}}
                <ul class="nav nav-tabs mt-auto mb-4" id="profile-tab" role="tablist">
                    <li class="nav-item">
                        <a
                                class="nav-link active"
                                id="tab-contact"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-pane-contact"
                                href="#tab-pane-contact"
                                role="tab"
                                aria-controls="#tab-pane-contact"
                                aria-selected="true"
                                save-tab="contact"
                        >{{ __('locations.campus.information.contact') }}</a>
                    </li>
                    <li class="nav-item">
                        <a
                                class="nav-link"
                                id="tab-maps"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-pane-maps"
                                href="#tab-pane-maps"
                                role="tab"
                                aria-controls="#tab-pane-maps"
                                aria-selected="false"
                                save-tab="maps"
                        >{{ __('locations.areas.maps') }}</a>
                    </li>
                    <li class="nav-item">
                        <a
                                class="nav-link"
                                id="tab-rooms"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-pane-rooms"
                                href="#tab-pane-rooms"
                                role="tab"
                                aria-controls="#tab-pane-rooms"
                                aria-selected="false"
                                save-tab="rooms"
                        >{{ trans_choice('locations.rooms',2) }}</a>
                    </li>
                </ul>
                {{-- Tab Content --}}
                <div class="tab-content profile-tab" id="profile-tab-content">
                    <div
                            class="tab-pane fade show active"
                            id="tab-pane-contact" role="tabpanel" aria-labelledby="tab-contact" tabindex="0"
                    >
                        <ul class="list-group">
                            @if($building->address)
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-top">
                                        <label>
                                            {{ __('locations.buildings.address') }}
                                        </label>
                                        <span>{!! nl2br($building->address->pretty_address) !!}</span>
                                    </div>
                                </li>
                            @endif
                            @if($building->phones()->count() > 0)
                                @foreach($building->phones as $phone)
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-top">
                                            <label>
                                                @if($phone->personal->primary)
                                                    {{ __('addresses.primary') }}
                                                @endif
                                                {{ $phone->personal->label }}
                                                {{ trans_choice('phones.phone',1) }}
                                            </label>
                                            <span>{{ $phone->pretty_phone }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    <div
                            class="tab-pane fade"
                            id="tab-pane-rooms" role="tabpanel" aria-labelledby="tab-rooms" tabindex="0"
                    >
                        <ul class="list-group">
                            @foreach($building->rooms as $room)
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1 room-display d-none"
                                    area-id="{{ $room->buildingArea->id }}">
                                    <div class="d-flex justify-content-between align-items-top">
                                        <label>
                                            {{ $room->buildingArea->schoolArea->name }}
                                        </label>
                                        <a href="{{ route('locations.rooms.show', $room->id) }}" class="link-primary">{{ $room->name }}</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div
                            class="tab-pane fade"
                            id="tab-pane-maps" role="tabpanel" aria-labelledby="tab-maps" tabindex="0"
                    >
                        <div id="blueprint-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
