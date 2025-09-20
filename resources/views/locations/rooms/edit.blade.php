@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="row profile-head-row">
            <div class="col-md-4">
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
            </div>
            <div class="col-md-6">
                <div class="profile-head d-flex align-items-start flex-column h-100">
                    <form id="basic-info-form" action="{{ route('locations.rooms.update.basic', $room) }}" method="POST"
                          class="w-100">
                        @csrf
                        @method('PUT')
                        <h5 class="w-100">
                            <div class="form-floating">
                                <input
                                        type="text"
                                        class="form-control @error('name') is-invalid @enderror"
                                        id="name"
                                        name="name"
                                        placeholder="{{ __('locations.rooms.name') }}"
                                        value="{{ $room->name }}"
                                        onchange="$('#basic-info-form').submit()"
                                />
                                <label for="name">{{ __('locations.rooms.name') }}</label>
                                <x-error-display key="name">{{ $errors->first('name') }}</x-error-display>
                            </div>
                        </h5>
                        <h6 class="d-flex flex-column">
                            <div class="form-floating align-self-start">
                                <input
                                        type="number"
                                        class="form-control @error('capacity') is-invalid @enderror"
                                        id="capacity"
                                        name="capacity"
                                        placeholder="{{ __('locations.rooms.capacity') }}"
                                        value="{{ $room->capacity }}"
                                        onchange="$('#basic-info-form').submit()"
                                />
                                <label for="capacity">{{ __('locations.rooms.capacity') }}</label>
                                <x-error-display key="capacity">{{ $errors->first('capacity') }}</x-error-display>
                            </div>
                        </h6>
                    </form>
                    <ul class="nav nav-tabs mt-auto" id="profile-tab" role="tablist">
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
                            >{{ __('people.profile.basic') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-2">
                <a
                        type="button"
                        class="btn btn-danger profile-edit-btn"
                        href="{{ route('locations.rooms.show', $room) }}"
                >{{ __('locations.rooms.editing') }}</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="profile-work">
                    <form id="basic-info-form" action="{{ route('locations.rooms.update.campuses', $room) }}"
                          method="POST" class="w-100">
                        @csrf
                        @method('PUT')
                        <p>{{ trans_choice('locations.campus', \App\Models\Locations\Campus::count()) }}</p>
                        @error('campuses')
                        <div class="alert alert-danger">{{ $errors->first('campuses') }}</div>
                        @enderror
                        <ul class="list-group">
                            @foreach(\App\Models\Locations\Campus::all() as $campus)
                                <li class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center border-bottom">
                                        {{ $campus->abbr }}
                                        <div class="form-check form-switch">
                                            <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    role="switch"
                                                    id="campus_{{ $campus->id }}"
                                                    name="campuses[]"
                                                    onclick="toggleCampus('{{ $campus->id }}')"
                                                    value="{{ $campus->id }}"
                                                    @if($room->campuses()->where('campus_id', $campus->id)->exists())
                                                        checked
                                                    @endif
                                            >
                                        </div>
                                    </div>
                                    <div
                                            class="row mt-1 @if(!$room->campuses()->where('campus_id', $campus->id)->exists()) d-none @endif"
                                            id="campus-info-{{ $campus->id }}"
                                    >
                                        <div class="col align-self-center">
                                            <div class="form-check">
                                                <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        value="1"
                                                        id="classroom_{{ $campus->id }}"
                                                        name="classroom_{{ $campus->id }}"
                                                        @if($room->campuses()->where('campus_id', $campus->id)->exists() &&
                                                            $room->campuses()->where('campus_id', $campus->id)->first()->info->classroom)
                                                            checked
                                                        @endif
                                                >
                                                <label class="form-check-label" for="classroom_{{ $campus->id }}">
                                                    {{ __('locations.rooms.classroom') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-floating">
                                                <input
                                                        type="text"
                                                        class="form-control"
                                                        id="label_{{ $campus->id }}"
                                                        name="label_{{ $campus->id }}"
                                                        placeholder="{{ __('locations.rooms.label') }}"
                                                        @if($room->campuses()->where('campus_id', $campus->id)->exists())
                                                            value="{{ $room->campuses()->where('campus_id', $campus->id)->first()->info->label }}"
                                                        @endif
                                                />
                                                <label for="label_{{ $campus->id }}">{{ __('locations.rooms.label') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <button
                                type="submit"
                                class="btn btn-primary mt-2 w-100"
                        >{{ trans_choice('locations.campus.update', \App\Models\Locations\Campus::count()) }}</button>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="tab-content profile-tab" id="profile-tab-content">
                    <div
                            class="tab-pane fade show active"
                            id="tab-pane-basic" role="tabpanel" aria-labelledby="tab-basic" tabindex="0"
                    >
                        <div class="mb-3 p-1">
                            <livewire:phone-editor :phoneable="$room"/>
                        </div>

                        <livewire:locations.room-type-editor :room="$room"/>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        function toggleCampus(campus_id) {
            let checked = $('#campus_' + campus_id).is(':checked');
            if (!checked) {
                $('#campus-info-' + campus_id).addClass('d-none');
                $('#classroom_' + campus_id).prop('checked', false);
                $('#label_' + campus_id).val('');
            } else {
                $('#campus-info-' + campus_id).removeClass('d-none');
            }

        }
    </script>
@endpush
