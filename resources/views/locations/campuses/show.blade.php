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
                                src="{{ $campus->img }}"
                                alt="{{ __('locations.campus.img') }}"
                        />
                    </div>
                    {{-- Side Menu --}}
                    <div class="profile-work">
                        <p>{{ trans_choice('crud.level',$campus->levels->count()) }}</p>
                        @foreach($campus->levels as $level)
                            <a href="#">{{ $level->name }}</a><br/>
                        @endforeach

                        <p>{{ trans_choice('locations.buildings',$campus->buildings()->count()) }}</p>
                        @foreach($campus->buildings() as $building)
                            <a href="{{ route('locations.buildings.show', ['building' => $building->id]) }}">{{ $building->name }}</a>
                            <br/>
                        @endforeach

                        <p>{{ __('locations.academics') }}</p>
                        <a href="{{ route('subjects.subjects.index', ['campus' => $campus->id]) }}">{{ trans_choice('subjects.subject', 2) }}</a><br/>
                        <a href="{{ route('subjects.courses.index', ['subject' => $campus->subjects()->first()->id]) }}">{{ trans_choice('subjects.course', 2) }}</a><br/>

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
                                {{ $campus->name }}
                                ({{ $campus->abbr }})
                            </h5>
                            <h6 class="d-flex flex-column">
                                @if($campus->title)
                                    <p class="lead mb-2">"{{ $campus->title }}"</p>
                                @endif
                                {!! $campus->iconHtml("large", "align-self-start") !!}
                            </h6>

                        </div>
                    </div>
                    {{-- User Control --}}
                    <div class="col-md-4">
                        <div class="d-flex flex-column align-items-center">
                            <a
                                    type="button"
                                    class="btn btn-secondary profile-edit-btn"
                                    href="{{ route('locations.campuses.edit', ['campus' => $campus->id]) }}"
                            >{{ __('locations.campus.edit') }}</a>
                        </div>
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
                                id="tab-periods"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-pane-periods"
                                href="#tab-pane-periods"
                                role="tab"
                                aria-controls="#tab-pane-periods"
                                aria-selected="false"
                                save-tab="periods"
                        >{{ trans_choice('locations.period',2) }}</a>
                    </li>
                    <li class="nav-item">
                        <a
                                class="nav-link"
                                id="tab-blocks"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-pane-blocks"
                                href="#tab-pane-blocks"
                                role="tab"
                                aria-controls="#tab-pane-blocks"
                                aria-selected="false"
                                save-tab="blocks"
                        >{{ trans_choice('locations.block',2) }}</a>
                    </li>
                    <li class="nav-item">
                        <a
                                class="nav-link"
                                id="tab-grades"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-pane-grades"
                                href="#tab-pane-grades"
                                role="tab"
                                aria-controls="#tab-pane-grades"
                                aria-selected="false"
                                save-tab="grades"
                        >{{ __('learning.grades.translations') }}</a>
                    </li>
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content profile-tab" id="profile-tab-content">
                    <div
                            class="tab-pane fade show active"
                            id="tab-pane-contact" role="tabpanel" aria-labelledby="tab-contact" tabindex="0"
                    >
                        <ul class="list-group">
                            @foreach($campus->addresses as $address)
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-top">
                                        <label>
                                            @if($address->personal->primary)
                                                {{ __('addresses.primary') }}
                                            @endif
                                            {{ $address->personal->label }}
                                            {{ __('addresses.address') }}:
                                        </label>
                                        <span>{!! nl2br($address->pretty_address) !!}</span>
                                    </div>
                                </li>
                            @endforeach
                            @if($campus->phones()->count() > 0)
                                @foreach($campus->phones as $phone)
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-top">
                                            <label>
                                                @if($phone->personal->primary)
                                                    {{ __('addresses.primary') }}
                                                @endif
                                                {{ $phone->personal->label }}
                                                {{ __('phones.phone') }}:
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
                                    <a href="{{ route('locations.buildings.show', $building) }}"><i class="fa-solid fa-eye"></i></a>
                                </h3>
                                @foreach($campus->buildingAreas()->where('building_id', $building->id) as $area)
                                    <div class="ms-3 mb-3">
                                        <h4 class="border-bottom d-flex justify-content-between align-items-end">
                                            {{ $area->name }}
                                            <a href="{{ route('locations.areas.show', $area) }}"><i class="fa-solid fa-eye"></i></a>
                                        </h4>
                                        @foreach($campus->rooms->where('area_id', $area->id) as $room)
                                            <div class="d-flex border-bottom justify-content-between align-items-end">
                                                <span class="col-4">
                                                    @if($room->info->label)
                                                        {{ $room->info->label }}
                                                        @if($room->info->classroom)
                                                            ({{ __('locations.rooms.classroom') }})
                                                        @endif
                                                    @elseif($room->info->classroom)
                                                        {{ __('locations.rooms.classroom') }}
                                                    @endif
                                                </span>
                                                <span>
                                                    <a
                                                            href="{{ route('locations.rooms.show', $room) }}"
                                                            class="text-decoration-none"
                                                    >{{ $room->name }}</a>
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <div
                            class="tab-pane fade"
                            id="tab-pane-periods" role="tabpanel" aria-labelledby="tab-periods" tabindex="0"
                    >
                        @foreach(\App\Classes\Settings\Days::weekdaysOptions() as $dayId => $dayName)
                            <div class="mb-3">
                                <h3 class="border-bottom d-flex justify-content-between align-items-end">
                                    {{ $dayName }}
                                </h3>
                                @forelse($campus->periods($dayId)->get() as $period)
                                    <div class="period d-flex border-bottom justify-content-between align-items-end @if(!$period->active) inactive d-none @endif">
                                        <span class="col-4 @if(!$period->active) text-warning @endif">
                                            {{ $period->name }} ( {{ $period->abbr }})
                                        </span>
                                        <span>
                                            {{ $period->dayStr() }} {{ $period->start->format('g:i A') }} &mdash; {{ $period->end->format('g:i A') }}
                                        </span>
                                    </div>
                                @empty
                                    <h4 class="text-center mb-5">{{ __('locations.period.no') }}</h4>
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                    <div
                            class="tab-pane fade"
                            id="tab-pane-blocks" role="tabpanel" aria-labelledby="tab-blocks" tabindex="0"
                    >

                        @forelse($campus->blocks()->active()->get() as $block)
                            <div class="d-flex border-bottom justify-content-between align-items-end block">
                                <span class="col-4">{{ $block->name }}</span>
                                <span>{{ $block->periods->implode('abbr', ', ') }}</span>
                            </div>
                        @empty
                            <h4 class="text-center mb-5">{{ __('locations.block.no') }}</h4>
                        @endforelse
                    </div>

                    <div
                            class="tab-pane fade"
                            id="tab-pane-grades" role="tabpanel" aria-labelledby="tab-grades" tabindex="0"
                    >
                        <x-learning.view-grade-translations :schema="$schema"></x-learning.view-grade-translations>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
