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
                    <form action="{{ route('locations.buildings.update.img', ['building' => $building->id]) }}"
                          method="POST">
                        @csrf
                        @method('PUT')
                        <div class="input-group w-100">
                            <label for="img" class="input-group-text">{{ __('locations.buildings.img') }}</label>
                            <input
                                    type="url"
                                    class="form-control"
                                    id="img"
                                    name="img"
                                    placeholder="{{ __('locations.buildings.img') }}"
                                    value="{{ $building->img }}"
                            />
                            <button type="submit" class="btn btn-primary"><i
                                        class="fa-solid fa-floppy-disk"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="profile-head d-flex align-items-start flex-column h-100">
                    <form
                            class="w-100"
                            action="{{ route('locations.buildings.update.basic', ['building' => $building->id]) }}"
                            method="POST"
                            id="basic-info-form"
                    >
                        @csrf
                        @method('PUT')
                        <h5 class="w-100">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input
                                                type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                id="name"
                                                name="name"
                                                placeholder="{{ __('locations.campus.name') }}"
                                                value="{{ $building->name }}"
                                                onchange="$('#basic-info-form').submit()"
                                        />
                                        <label for="name">{{ __('locations.buildings.name') }}</label>
                                        <x-utilities.error-display key="name">{{ $errors->first('name') }}</x-utilities.error-display>
                                    </div>
                                </div>
                            </div>
                        </h5>
                    </form>
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
                                    id="tab-levels"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-pane-levels"
                                    href="#tab-pane-levels"
                                    role="tab"
                                    aria-controls="#tab-pane-levels"
                                    aria-selected="true"
                            >{{ trans_choice('locations.buildings.areas', 2) }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-2">
                <a
                        type="button"
                        class="btn btn-danger profile-edit-btn"
                        href="{{ route('locations.buildings.show', ['building' => $building->id]) }}"
                >{{ __('locations.buildings.editing') }}</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="profile-work">
                </div>
            </div>
            <div class="col-md-6">
                <div class="tab-content profile-tab" id="profile-tab-content">
                    <div
                            class="tab-pane fade show active"
                            id="tab-pane-contact" role="tabpanel" aria-labelledby="tab-contact" tabindex="0"
                    >
                        <div class="mb-3 p-1">
                            <livewire:address-editor :addressable="$building"/>
                        </div>

                        <div class="mb-3 p-1">
                            <livewire:phone-editor :phoneable="$building"/>
                        </div>
                    </div>
                    <div
                            class="tab-pane fade"
                            id="tab-pane-levels" role="tabpanel" aria-labelledby="tab-levels" tabindex="0"
                    >
                        <form action="{{ route('locations.buildings.update.areas', ['building' => $building->id]) }}"
                              method="POST">
                            @csrf
                            @method('PUT')
                            @error('areas')
                            <div class="alert alert-danger">{{ __('errors.buildings.areas') }}</div>
                            @enderror
                            <ul class="list-group">
                                @foreach(\App\Models\SystemTables\SchoolArea::all() as $area)
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-top">
                                            <label for="areas_{{ $area->id }}">
                                                {{ $area->name }}
                                            </label>
                                            <span class="form-check form-switch">
                                            <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    role="switch"
                                                    id="areas_{{ $area->id }}"
                                                    name="areas[]"
                                                    value="{{ $area->id }}"
                                                    @if($building->schoolAreas()->where('buildings_areas.area_id', $area->id)->exists())
                                                        checked
                                                    @if(!$building->canRemoveArea($area)) disabled @endif
                                                    @endif
                                            />
                                            @if(!$building->canRemoveArea($area))
                                                    <input type="hidden" name="areas[]" value="{{ $area->id }}"/>
                                                @endif
                                        </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <button type="submit" class=" mt-3 btn btn-primary w-100">{{ __('common.update') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
