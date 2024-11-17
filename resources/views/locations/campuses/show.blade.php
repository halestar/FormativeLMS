@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="row profile-head-row">
            <div class="col-md-4">
                <div class="profile-img">
                    <img
                        class="img-fluid img-thumbnail"
                        src="{{ $campus->img }}"
                        alt="{{ __('locations.campus.img') }}"
                    />
                </div>
            </div>
            <div class="col-md-6">
                <div class="profile-head d-flex align-items-start flex-column h-100">
                    <h5>
                        {{ $campus->name }}
                        ({{ $campus->abbr }})
                    </h5>
                    <h6 class="d-flex flex-column">
                        @if($campus->title)
                        <p class="lead mb-2">"{{ $campus->title }}"</p>
                        @endif
                        {!! $campus->iconHtml("large", "align-self-start") !!}
                    </h6>
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
                            >{{ trans_choice('locations.rooms',2) }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-2">
                <a
                    type="button"
                    class="btn btn-secondary profile-edit-btn"
                    href="{{ route('locations.campuses.edit', ['campus' => $campus->id]) }}"
                >{{ __('locations.campus.edit') }}</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="profile-work">
                    <p>{{ trans_choice('crud.level',$campus->levels->count()) }}</p>
                    @foreach($campus->levels as $level)
                        <a href="#">{{ $level->name }}</a><br/>
                    @endforeach

                    <p>{{ trans_choice('locations.buildings',$campus->buildings()->count()) }}</p>
                    @foreach($campus->buildings() as $building)
                        <a href="{{ route('locations.buildings.show', ['building' => $building->id]) }}">{{ $building->name }}</a><br/>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                <div class="tab-content profile-tab" id="profile-tab-content">
                    <div
                        class="tab-pane fade show active"
                        id="tab-pane-contact" role="tabpanel" aria-labelledby="tab-contact" tabindex="0"
                    >
                            <ul class="list-group">
                                @if($campus->address)
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-top">
                                        <label>
                                            {{ __('locations.campus.address') }}
                                        </label>
                                        <span>{!! nl2br($campus->address->pretty_address) !!}</span>
                                    </div>
                                </li>
                                @endif
                                @if($campus->phones()->count() > 0)
                                    @foreach($campus->phones as $phone)
                                            <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                                <div class="d-flex justify-content-between align-items-top">
                                                    <label>
                                                        {{ $phone->personal->label }}
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
                        @foreach($campus->buildings() as $building)
                            <div class="mb-3">
                                <h3 class="border-bottom d-flex justify-content-between align-items-end">
                                    {{ $building->name }}
                                </h3>
                                @foreach($campus->buildingAreas()->where('building_id', $building->id) as $area)
                                    <div class="ms-3 mb-3">
                                        <h4 class="border-bottom d-flex">
                                            {{ $area->name }}
                                        </h4>
                                        @foreach($campus->rooms->where('area_id', $area->id) as $room)
                                            <div class="d-flex border-bottom justify-content-between align-items-end">
                                                <span class="col-4">
                                                    @if($room->info->classroom) {{ __('locations.rooms.classroom') }}@endif
                                                    {{ $room->info->label }}
                                                </span>
                                                <span>{{ $room->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
