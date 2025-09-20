@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="border-bottom d-flex justify-content-between align-items-baseline mb-3 @if(count($errors)) d-none @endif"
             id="add-header">
            <h2>{{ __('system.menu.rooms') }}</h2>

            <div>
                <button
                        class="btn btn-primary"
                        type="button"
                        onclick="$('#add-container,#add-header').toggleClass('d-none')"
                ><i class="fa-solid fa-plus border-end pe-2 me-2"></i>{{ __('locations.buildings.add') }}</button>
            </div>
        </div>

        <div class="card mb-2 text-bg-light @if(!count($errors)) d-none @endif" id="add-container">
            <form action="{{ route('locations.buildings.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-10">
                            <label for="name" class="form-label">{{ __('locations.buildings.name') }}</label>
                            <input
                                    type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    name="name"
                                    id="name"
                            />
                            <x-error-display key="name">{{ $errors->first('name') }}</x-error-display>
                        </div>
                        <div class="col-2 align-self-end text-center">
                            <button type="submit" class="btn btn-primary">{{ __('locations.buildings.add') }}</button>
                            <button
                                    type="button"
                                    onclick="$('#add-container,#add-header').toggleClass('d-none')"
                                    class="btn btn-secondary"
                            >{{ __('common.cancel') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="accordion mt-2" id="building-container">
            <div
                    class="accordion-item"
                    style="background-image: url('/images/free-floating-rooms.png'); background-attachment: fixed; background-size: cover; background-position: center center; background-repeat: no-repeat;"
            >
                <h3 class="accordion-header d-flex justify-content-between align-items-center">
                    <div class="d-flex justify-content-center align-items-center w-100">
                        <span class="ms-3 me-auto  badge text-bg-info">
                            {{ trans_choice('locations.rooms.free',\App\Models\Locations\Room::freeFloating()->count()) }}
                        </span>
                        <span class="badge text-bg-primary">
                            {{ \App\Models\Locations\Room::freeFloating()->count() }} {{ trans_choice('locations.rooms', \App\Models\Locations\Room::freeFloating()->count()) }}
                        </span>
                        <span class="ms-auto me-5">
                            <a
                                    href="{{ route('locations.rooms.create') }}"
                                    class="btn btn-info btn-sm"
                                    role="button"
                            ><i class="fa-solid fa-plus border-end pe-2 me-2"></i>{{ __('locations.rooms.add') }}</a>
                        </span>
                    </div>
                    <button
                            class="accordion-button collapsed ms-auto w-auto opacity-50"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#building_free"
                            aria-expanded="false"
                            aria-controls="building_free"
                    ></button>
                </h3>
                <div id="building_free" class="accordion-collapse collapse" data-bs-parent="#building-container">
                    <div class="accordion-body">
                        <ul class="list-group list-group-flush">
                            @foreach(\App\Models\Locations\Room::freeFloating()->get() as $room)
                                <li
                                        class="list-group-item list-group-item-action opacity-75"
                                >
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="room-name">{{ $room->name }}</span>
                                        <span>
                                                <a
                                                        href="{{ route('locations.rooms.show', $room) }}"
                                                        class="btn btn-primary btn-sm"
                                                ><i class="fa-solid fa-eye"></i></a>
                                                @if($room->canDelete())
                                                <a
                                                        href="#"
                                                        class="btn btn-danger btn-sm"
                                                ><i class="fa-solid fa-times"></i></a>
                                            @endif
                                            </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @foreach(\App\Models\Locations\Building::all() as $building)
                <div
                        class="accordion-item"
                        @if($building->img)
                            style="background-image: url('{{ $building->img }}'); background-attachment: fixed; background-size: cover; background-position: center center; background-repeat: no-repeat;"
                        @endif
                >
                    <h3 class="accordion-header d-flex justify-content-between align-items-center">
                        <div class="d-flex justify-content-center align-items-center w-100">
                            <span class="ms-3 me-auto badge text-bg-info">{{ $building->name }}</span>
                            <span class="badge text-bg-primary">{{ $building->rooms()->count() }} {{ trans_choice('locations.rooms',$building->rooms()->count()) }}</span>
                            <span class="ms-auto me-5">
                                <a
                                        href="{{ route('locations.buildings.show', ['building' => $building->id]) }}"
                                        class="btn btn-primary btn-sm"
                                ><i class="fa fa-eye"></i></a>
                                @if($building->canDelete())
                                    <button
                                            onclick="confirmDelete('{{ __('locations.buildings.delete.confirm') }}', '{{ route('locations.buildings.destroy', $building) }}')"
                                            class="btn btn-danger btn-sm"
                                    ><i class="fa fa-times"></i></button>
                                @endif
                                <a
                                        href="{{ route('locations.rooms.create', ['building' => $building->id]) }}"
                                        class="btn btn-info btn-sm"
                                        role="button"
                                ><i class="fa-solid fa-plus border-end pe-2 me-2"></i>{{ __('locations.rooms.add') }}</a>
                            </span>
                        </div>
                        <button
                                class="accordion-button collapsed ms-auto w-auto opacity-50"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#building_{{ $building->id }}"
                                aria-expanded="false"
                                aria-controls="building_{{ $building->id }}"
                        ></button>
                    </h3>
                    <div id="building_{{ $building->id }}" class="accordion-collapse collapse"
                         data-bs-parent="#building-container">
                        <div class="accordion-body">
                            <ul class="list-group list-group-flush opacity-75">
                                @foreach($building->rooms as $room)
                                    <li
                                            class="list-group-item list-group-item-action"
                                    >
                                        <div class="row">
                                            <div class="col-6 align-self-center">{{ $room->name }}</div>
                                            <div class="col-2 align-self-center">
                                                <span class="badge text-bg-info">{{ $room->buildingArea->schoolArea->name }}</span>
                                            </div>
                                            <div class="col-2 d-flex justify-content-start align-items-center">
                                                @foreach($room->campuses as $campus)
                                                    {!! $campus->iconHtml('normal') !!}
                                                @endforeach
                                            </div>
                                            <div class="col-2 text-end align-self-center">
                                                <a
                                                        href="{{ route('locations.rooms.show', $room) }}"
                                                        class="btn btn-primary btn-sm"
                                                ><i class="fa-solid fa-eye"></i></a>
                                                @if($room->canDelete())
                                                    <a
                                                            href="#"
                                                            class="btn btn-danger btn-sm"
                                                    ><i class="fa-solid fa-times"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
