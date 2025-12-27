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
                    <form action="{{ route('locations.buildings.update.img', ['building' => $building->id]) }}"
                          method="POST" enctype="multipart/form-data"
                          x-data="{ docData: null }"
                          x-ref="imgForm"
                          x-on:document-storage-browser-files-selected.window="
                                        if($event.detail.cb_instance === 'building-img')
                                        {
                                            docData=JSON.stringify($event.detail.selected_items);
                                            $nextTick(() => { $refs.imgForm.submit() });
                                        }"
                    >
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="building_img" x-model="docData" />
                        <button
                            type="button"
                            class="file btn btn-lg btn-dark"
                            @click="$dispatch('document-storage-browser.open-browser',
                                        {
                                            config:
                                                {
                                                    multiple: false,
                                                    mimetypes: {{ Js::from(\App\Models\Utilities\MimeType::imageMimeTypes()) }},
                                                    allowUpload: true,
                                                    canSelectFolders: false,
                                                    cb_instance: 'building-img'
                                                }
                                        });"
                        >
                            {{ __('locations.buildings.img.update') }}
                        </button>
                    </form>
                </div>
                {{-- Side Menu --}}
                <div class="profile-work">
                    <p>{{ trans_choice('locations.buildings.areas', 2) }}</p>
                    <form action="{{ route('locations.buildings.update.areas', ['building' => $building->id]) }}"
                          method="POST">
                        @csrf
                        @method('PUT')
                        @error('areas')
                        <div class="alert alert-danger">{{ __('errors.buildings.areas') }}</div>
                        @enderror
                        <ul class="list-group">
                            @foreach(\App\Models\SystemTables\SchoolArea::all() as $area)
                                <li class="list-group-item list-group-item-action">
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
                                                    @checked($building->hasArea($area))
                                                    @disabled(!$building->canRemoveArea($area))
                                            />
                                            @if(!$building->canRemoveArea($area))
                                                <input type="hidden" name="areas[]" value="{{ $area->id }}" />
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
        {{-- Main Content Column --}}
        <div class="col-md-8">
            <div class="row mb-4">
                {{-- Basic Info --}}
                <div class="col-md-8">
                    <div class="profile-head d-flex align-items-start flex-column mb-4">
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
                                    aria-selected="true"
                                    save-tab="maps"
                            >{{ __('locations.areas.maps') }}</a>
                        </li>
                    </ul>
                    {{-- Tab Content --}}
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
                                id="tab-pane-maps" role="tabpanel" aria-labelledby="tab-maps" tabindex="0"
                        >
                            @foreach($building->buildingAreas as $area)
                                <form
                                    action="{{ route('locations.buildings.update.areas.map', ['building' => $building->id, 'area' => $area->id]) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('PUT')
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">{{ $area->name }}</span>
                                        <select class="form-select" name="map"
                                                id="map_{{ $area->id }}" onchange="this.form.submit()">
                                            @if(!$area->blueprint_url->isWorkFile())
                                            <option>{{ __('locations.buildings.maps.select') }}</option>
                                            @endif
                                            @foreach($building->workFiles as $map)
                                                <option
                                                        value="{{ $map->id }}"
                                                        @selected($area->blueprint_url->isWorkFile() && $area->blueprint_url->workFileId == $map->id)
                                                >{{ $map->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($area->blueprint_url)
                                            <a
                                                href="{{ route('locations.areas.show', $area) }}"
                                                class="btn btn-primary"
                                                role="button"
                                            ><i class="fa-solid fa-edit"></i></a>
                                        @endif
                                    </div>
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>
                {{-- User Control --}}
                <div
                    class="col-md-4"
                    x-data
                    x-on:work-storage-browser-files-added="window.location.reload();"
                    x-on:work-storage-browser-file-removed="window.location.reload();"
                >
                    <div class="d-flex flex-column align-items-center mb-4">
                        <a
                                type="button"
                                class="btn btn-danger profile-edit-btn"
                                href="{{ route('locations.buildings.show', ['building' => $building->id]) }}"
                        >{{ __('locations.buildings.editing') }}</a>
                    </div>
                    <livewire:storage.work-storage-browser height="400px" :fileable="$building" :title="__('locations.buildings.maps')" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
