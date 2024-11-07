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
                    <form action="{{ route('locations.campuses.update.img', ['campus' => $campus->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="input-group w-100">
                            <label for="img" class="input-group-text">{{ __('locations.campus.img') }}</label>
                            <input
                                type="url"
                                class="form-control"
                                id="img"
                                name="img"
                                placeholder="{{ __('locations.campus.img') }}"
                                value="{{ $campus->img }}"
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
                        action="{{ route('locations.campuses.update.basic', ['campus' => $campus->id]) }}"
                        method="POST"
                        id="basic-info-form"
                    >
                        @csrf
                        @method('PUT')
                        <h5 class="w-100">
                            <div class="row">
                                <div class="col-8">
                                    <div class="form-floating">
                                        <input
                                            type="text"
                                            class="form-control @error('name') is-invalid @enderror"
                                            id="name"
                                            name="name"
                                            placeholder="{{ __('locations.campus.name') }}"
                                            value="{{ $campus->name }}"
                                            onchange="$('#basic-info-form').submit()"
                                        />
                                        <label for="name">{{ __('locations.campus.name') }}</label>
                                        <x-error-display key="name">{{ $errors->first('name') }}</x-error-display>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-floating">
                                        <input
                                            type="text"
                                            class="form-control @error('abbr') is-invalid @enderror"
                                            id="abbr"
                                            name="abbr"
                                            placeholder="{{ __('locations.campus.abbr') }}"
                                            value="{{ $campus->abbr }}"
                                            onchange="$('#basic-info-form').submit()"
                                        />
                                        <label for="abbr">{{ __('locations.campus.abbr') }}</label>
                                        <x-error-display key="abbr">{{ $errors->first('abbr') }}</x-error-display>
                                    </div>
                                </div>
                            </div>
                        </h5>
                        <h6 class="w-100">
                            <div class="form-floating">
                                <input
                                    type="text"
                                    class="form-control"
                                    id="title"
                                    name="title"
                                    placeholder="{{ __('locations.campus.title') }}"
                                    value="{{ $campus->title }}"
                                    onchange="$('#basic-info-form').submit()"
                                />
                                <label for="abbr">{{ __('locations.campus.title') }}</label>
                            </div>
                        </h6>
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
                            >{{ __('locations.campus.information.levels') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-2">
                <a
                    type="button"
                    class="btn btn-danger profile-edit-btn"
                    href="{{ route('locations.campuses.show', ['campus' => $campus->id]) }}"
                >{{ __('locations.campus.editing') }}</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="profile-work">
                    <form
                        class="d-flex flex-column justify-content-center mt-3"
                        enctype="multipart/form-data"
                        action="{{ route('locations.campuses.update.icon', ['campus' => $campus->id]) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PUT')
                        <div class="border rounded p-2 align-self-center" style="background-color: {{ $campus->color_pri }};" id="icon-container">
                            <div class="img-mini" style="color: {{ $campus->color_sec }};">{!! $campus->icon !!}</div>
                        </div>
                        <strong class="align-self-center">{{ __('locations.campus.icon') }}</strong>
                        <div class="form-floating">
                            <input
                                type="color"
                                class="form-control"
                                id="color_pri"
                                name="color_pri"
                                placeholder="{{ __('locations.campus.color_pri') }}"
                                value="{{ $campus->color_pri }}"
                                onchange="$('#icon-container').css('background-color', $(this).val())"
                            />
                            <label for="abbr">{{ __('locations.campus.color_pri') }}</label>
                        </div>
                        <div class="form-floating mt-1">
                            <input
                                type="color"
                                class="form-control"
                                id="color_sec"
                                name="color_sec"
                                placeholder="{{ __('locations.campus.color_sec') }}"
                                value="{{ $campus->color_sec }}"
                                onchange="$('#icon-container .img-mini').css('color', $(this).val())"
                            />
                            <label for="abbr">{{ __('locations.campus.color_sec') }}</label>
                        </div>
                        <div class="mt-1 mb-3">
                            <label for="icon" class="form-label">{{ __('locations.campus.icon') }}</label>
                            <input
                                class="form-control"
                                type="file"
                                id="icon"
                                name="icon"
                                accept="image/svg+xml"
                                aria-describedby="icon-help"
                                onchange="setIcon()"
                            />
                            <div id="icon-help" class="form-text">
                                {!! __('locations.campus.icon.help') !!}
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="tab-content profile-tab" id="profile-tab-content">
                    <div
                        class="tab-pane fade show active"
                        id="tab-pane-contact" role="tabpanel" aria-labelledby="tab-contact" tabindex="0"
                    >
                        <form
                            class="border rounded p-2 text-bg-light mb-3"
                            action="{{ route('locations.campuses.update.address', ['campus' => $campus->id]) }}"
                            method="POST"
                        >
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-floating mb-0">
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="line1"
                                            name="line1"
                                            placeholder="{{ __('addresses.street_address') }}"
                                            autocomplete="off"
                                            value="{{ $campus->line1 }}"
                                        />
                                        <label for="line1">{{ __('addresses.address_line_1') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="form-floating mb-3">
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="line2"
                                            name="line2"
                                            placeholder="{{ __('addresses.address_line_2') }}"
                                            value="{{ $campus->line2 }}"
                                        />
                                        <label for="line2">{{ __('addresses.address_line_2') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="line3"
                                            name="line3"
                                            placeholder="{{ __('addresses.address_line_3') }}"
                                            value="{{ $campus->line3 }}"
                                        />
                                        <label for="line3">{{ __('addresses.address_line_3') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="city"
                                            name="city"
                                            placeholder="{{ __('addresses.city') }}"
                                            value="{{ $campus->city }}"
                                        />
                                        <label for="city" class="form-label">{{ __('addresses.city') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating mb-3">
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="state"
                                            name="state"
                                            placeholder="{{ __('addresses.state') }}"
                                            value="{{ $campus->state }}"
                                        />
                                        <label for="state" class="form-label">{{ __('addresses.state') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating mb-3">
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="zip"
                                            name="zip"
                                            placeholder="{{ __('addresses.zip') }}"
                                            value="{{ $campus->zip }}"
                                        >
                                        <label for="zip" class="form-label">{{ __('addresses.zip') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="country"
                                            name="country"
                                            placeholder="{{ __('addresses.country') }}"
                                            value="{{ $campus->country }}"
                                        />
                                        <label for="country" class="form-label">{{ __('addresses.country') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-6 align-self-center text-center">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >{{ __('addresses.update_address') }}</button>
                                </div>
                            </div>
                        </form>
                        <livewire:phone-editor :phoneable="$campus" />
                    </div>
                    <div
                        class="tab-pane fade"
                        id="tab-pane-levels" role="tabpanel" aria-labelledby="tab-levels" tabindex="0"
                    >
                        <form action="{{ route('locations.campuses.update.levels', ['campus' => $campus->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @error('levels')
                            <div class="alert alert-danger">{{ __('errors.campuses.levels') }}</div>
                            @enderror
                            <ul class="list-group">
                                @foreach(\App\Models\CRUD\Level::all() as $level)
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-top">
                                        <label for="levels_{{ $level->id }}">
                                            {{ $level->name }}
                                        </label>
                                        <span class="form-check form-switch">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                role="switch"
                                                id="levels_{{ $level->id }}"
                                                name="levels[]"
                                                value="{{ $level->id }}"

                                                @if($campus->levels()->where('id', $level->id)->exists())
                                                    checked
                                                    @if(!$campus->canRemoveLevel($level)) disabled @endif
                                                @endif
                                            />
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
@push('scripts')
    <script>
        function setIcon()
        {
            let fl_files = event.target.files; // JS FileList object

            // use the 1st file from the list
            let fl_file = fl_files[0];
            let reader = new FileReader(); // built in API

            let display_file = ( e ) =>
            {
                $('#icon-container .img-mini').html(e.target.result);
            };

            let on_reader_load = ( fl ) => {
                return display_file; // a function
            };

            // Closure to capture the file information.
            reader.onload = on_reader_load( fl_file );

            // Read the file as text.
            reader.readAsText( fl_file );
        }
    </script>
@endpush
