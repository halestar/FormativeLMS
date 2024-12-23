@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="row profile-head-row">
            <div class="col-md-4">
                <div class="profile-img">
                    <img
                        class="img-fluid img-thumbnail"
                        src="{{ $building->img }}"
                        alt="{{ __('locations.buildings.img') }}"
                    />
                </div>
            </div>
            <div class="col-md-6">
                <div class="profile-head d-flex align-items-start flex-column h-100">
                    <h5>
                        {{ $building->name }}
                    </h5>
                    <ul class="nav nav-tabs mt-auto" id="profile-tab" role="tablist">
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
                    </ul>
                </div>
            </div>
            <div class="col-md-2">
                <a
                    type="button"
                    class="btn btn-secondary profile-edit-btn"
                    href="{{ route('locations.buildings.edit', ['building' => $building->id]) }}"
                >{{ __('locations.buildings.edit') }}</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="profile-work">
                    <p>{{ trans_choice('locations.buildings.areas', $building->buildingAreas()->count()) }}</p>
                    <ul class="list-group">
                        @foreach($building->buildingAreas as $area)
                            <li class="list-group-item d-flex justify-content-between align-items-center area-control list-group-item-action" area-id="{{ $area->id }}">
                                {{ $area->schoolArea->name }}
                                <div>
                                    <a
                                        href="{{ route('locations.areas.show', $area) }}"
                                        class="btn btn-primary btn-sm"
                                    ><i class="fa-solid fa-eye"></i></a>
                                    <button
                                        onclick="filterArea({{ $area->id }})"
                                        class="btn btn-outline-success btn-sm ms-1"
                                        save-fn="filterArea({{ $area->id }})"
                                    ><i class="fa-solid fa-filter"></i></button>
                                </div>

                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
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
                                                    @if($phone->personal->primary){{ __('addresses.primary') }}@endif
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
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1 room-display d-none" area-id="{{ $room->buildingArea->id }}">
                                    <div class="d-flex justify-content-between align-items-top">
                                        <label>
                                            {{ $room->buildingArea->schoolArea->name }}
                                        </label>
                                        <span>{{ $room->name }}</span>
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
@push('scripts')
    <script>
        function filterArea(area_id)
        {
            //remove all the old filters
            $('.area-control[area-id]').removeClass('active');
            $('.area-control[area-id] button.btn-success').removeClass('btn-success')
                .addClass('btn-outline-success')
                .prop('disabled', false);
            $('.room-display[area-id]').addClass('d-none');
            $('#blueprint-container').empty();

            $('.area-control[area-id=' + area_id + ']').addClass('active');
            $('.area-control[area-id=' + area_id + '] button.btn-outline-success').removeClass('btn-outline-success')
                .addClass('btn-success')
                .prop('disabled', true);
            $('.room-display[area-id=' + area_id + ']').removeClass('d-none');
            new MapDrawings('blueprint-container', area_id);
        }

        @if($building->buildingAreas->count() > 0)
            filterArea({{ $building->buildingAreas->first()->id }});
        @endif

    </script>
@endpush
