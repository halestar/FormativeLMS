@extends('layouts.app', ['breadcrumb' => $breadcrumb])
@push('head_scripts')
    <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable/build/umd/index.min.js"></script>
@endpush
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
                        <form action="{{ route('locations.campuses.update.img', ['campus' => $campus->id]) }}"
                              method="POST" enctype="multipart/form-data"
                              x-data="{ docData: null }"
                              x-ref="imgForm"
                              x-on:document-storage-browser-files-selected.window="
                                        if($event.detail.cb_instance === 'campus-img')
                                        {
                                            docData=JSON.stringify($event.detail.selected_items);
                                            $nextTick(() => { $refs.imgForm.submit() });
                                        }"
                        >
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="campus_img" x-model="docData" />
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
                                                        cb_instance: 'campus-img'
                                                    }
                                            });"
                            >
                                {{ __('locations.campus.img.update') }}
                            </button>
                        </form>
                    </div>
                    {{-- Side Menu --}}
                    <div class="profile-work">
                        <form
                                class="d-flex flex-column justify-content-center mt-3"
                                enctype="multipart/form-data"
                                action="{{ route('locations.campuses.update.icon', ['campus' => $campus->id]) }}"
                                method="POST"
                        >
                            @csrf
                            @method('PUT')
                            {!! $campus->iconHtml("xl", "align-self-center") !!}
                            <strong class="align-self-center">{{ __('locations.campus.icon') }}</strong>
                            <div class="form-floating">
                                <input
                                        type="color"
                                        class="form-control"
                                        id="color_pri"
                                        name="color_pri"
                                        placeholder="{{ __('locations.campus.color_pri') }}"
                                        value="{{ $campus->color_pri }}"
                                        onchange="$('.icon-container').css('background-color', $(this).val())"
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
                                        onchange="$('.icon-container .campus-icon-xl').css('color', $(this).val())"
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
            </div>
            {{-- Main Content Column --}}
            <div class="col-md-8">
                <div class="row mb-4">
                    {{-- Basic Info --}}
                    <div class="col-md-8">
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
                                                <x-utilities.error-display
                                                        key="name">{{ $errors->first('name') }}</x-utilities.error-display>
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
                                                <x-utilities.error-display
                                                        key="abbr">{{ $errors->first('abbr') }}</x-utilities.error-display>
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
                        </div>
                    </div>
                    {{-- User Control --}}
                    <div class="col-md-4">
                        <div class="d-flex flex-column align-items-center">
                            <a
                                    type="button"
                                    class="btn btn-danger profile-edit-btn"
                                    href="{{ route('locations.campuses.show', ['campus' => $campus->id]) }}"
                            >{{ __('locations.campus.editing') }}</a>
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
                        >{{ trans_choice('locations.rooms', 2) }}</a>
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
                    <li class="nav-item">
                        <a
                                class="nav-link"
                                id="tab-levels"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-pane-levels"
                                href="#tab-pane-levels"
                                role="tab"
                                aria-controls="#tab-pane-levels"
                                aria-selected="false"
                                save-tab="levels"
                        >{{ __('locations.campus.information.levels') }}</a>
                    </li>
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content profile-tab" id="profile-tab-content">
                    <div
                            class="tab-pane fade show active"
                            id="tab-pane-contact" role="tabpanel" aria-labelledby="tab-contact" tabindex="0"
                    >
                        <div class="mb-3 p-1">
                            <livewire:address-editor :addressable="$campus"/>
                        </div>

                        <div class="mb-3 p-1">
                            <livewire:phone-editor :phoneable="$campus"/>
                        </div>
                    </div>
                    <div
                            class="tab-pane fade"
                            id="tab-pane-levels" role="tabpanel" aria-labelledby="tab-levels" tabindex="0"
                    >
                        <form action="{{ route('locations.campuses.update.levels', ['campus' => $campus->id]) }}"
                              method="POST">
                            @csrf
                            @method('PUT')
                            @error('levels')
                            <div class="alert alert-danger">{{ __('errors.campuses.levels') }}</div>
                            @enderror
                            <ul class="list-group">
                                @foreach(\App\Models\SystemTables\Level::all() as $level)
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
                                            @if(!$campus->canRemoveLevel($level))
                                                <input type="hidden" name="areas[]" value="{{ $level->id }}"/>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <button type="submit" class=" mt-3 btn btn-primary w-100">{{ __('common.update') }}</button>
                        </form>
                    </div>
                    <div
                            class="tab-pane fade"
                            id="tab-pane-rooms" role="tabpanel" aria-labelledby="tab-rooms" tabindex="0"
                    >
                        <div class="mb-3 p-1">
                            <livewire:locations.campus-room-assigner :campus="$campus"/>
                        </div>
                    </div>
                    <div
                            class="tab-pane fade"
                            id="tab-pane-periods" role="tabpanel" aria-labelledby="tab-periods" tabindex="0"
                    >
                        <div class="mb-3 row">
                            <a
                                    class="btn btn-primary col mx-2"
                                    href="{{ route('locations.periods.create', ['campus' => $campus]) }}"
                            >{{ __('locations.period.new') }}</a>
                            <a
                                    class="col btn btn-info mx-2"
                                    href="{{ route('locations.periods.edit.mass', ['campus' => $campus]) }}"
                            >{{ __('locations.period.create.mass') }}</a>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input
                                    class="form-check-input"
                                    type="checkbox"
                                    role="switch"
                                    onclick="$('.period.inactive').toggleClass('d-none')"
                                    id="show-inactive-periods"
                            />
                            <label class="form-check-label"
                                   for="show-inactive-periods">{{ __('locations.period.inactive.show') }}</label>
                        </div>
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
                                            @can('edit', $period)
                                                <a
                                                        href="{{ route('locations.periods.edit', $period) }}"
                                                        class="btn btn-sm btn-primary ms-2"
                                                        role="button"
                                                >
                                                    <i class="fa-solid fa-edit"></i>
                                                </a>
                                                @if($period->canDelete())
                                                    <button
                                                            class="btn btn-danger btn-sm ms-1"
                                                            type="button"
                                                            onclick="confirmDelete('{{ __('locations.period.delete.confirm') }}', '{{ route('locations.periods.destroy', $period) }}')"
                                                    ><i class="fa-solid fa-times"></i></button>
                                                @endif
                                            @endcan
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
                        @can('create')
                            <div class="row" id="block-add-control">
                                <button
                                        type="button"
                                        class="btn btn-primary col mx-2"
                                        onclick="$('#block-add-control,#block-add-form').toggleClass('d-none')"
                                >{{ __('locations.block.create') }}</button>
                            </div>
                            <form action="{{ route('locations.blocks.store', ['campus' => $campus]) }}" method="POST"
                                  id="block-add-form-container" class="mb-4">
                                @csrf
                                <div class="row d-none" id="block-add-form">
                                    <div class="col-sm-8">
                                        <label for="block_name"
                                               class="form-label">{{ __('locations.block.name') }}</label>
                                        <input
                                                type="text"
                                                class="form-control @error('block_name') is-invalid @enderror"
                                                id="block_name"
                                                name="block_name"
                                        />
                                        <x-utilities.error-display
                                                key="block_name">{{ $errors->first('block_name') }}</x-utilities.error-display>
                                    </div>
                                    <div class="col-sm-4 align-self-end">
                                        <button type="submit"
                                                class="btn btn-primary">{{ __('locations.block.create') }}</button>
                                        <button
                                                type="button"
                                                class="btn btn-secondary"
                                                onclick="$('#block-add-control,#block-add-form').toggleClass('d-none')"
                                        >{{ __('common.cancel') }}</button>
                                    </div>
                                </div>
                            </form>
                        @endcan
                        <div class="form-check form-switch mb-3">
                            <input
                                    class="form-check-input"
                                    type="checkbox"
                                    role="switch"
                                    onclick="$('.block.inactive').toggleClass('d-none')"
                                    id="show-inactive-blocks"
                            />
                            <label class="form-check-label"
                                   for="show-inactive-blocks">{{ __('locations.block.inactive.show') }}</label>
                        </div>
                        <ul class="list-group block-list">
                            @forelse($campus->blocks as $block)
                                <li
                                    class="list-group-item d-flex justify-content-between align-items-end block @if(!$block->active) inactive d-none @endif"
                                    block-id="{{ $block->id }}"
                                >
                                    <div class="col-4 @if(!$block->active) text-warning @endif">
                                        <span class="block-move-handle me-2"><i
                                                    class="fa-solid fa-grip-lines-vertical"></i></span>
                                        {{ $block->name }}
                                    </div>
                                    <span>
                                        {{ $block->periods->implode('abbr', ', ') }}
                                        @can('edit', $block)
                                            <a
                                                href="{{ route('locations.blocks.edit', $block) }}"
                                                class="btn btn-primary btn-sm ms-2"
                                                role="button"
                                            >
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                            @if($block->canDelete())
                                                <button
                                                        class="btn btn-danger btn-sm ms-1"
                                                        type="button"
                                                        onclick="confirmDelete('{{ __('locations.block.delete.confirm') }}', '{{ route('locations.blocks.destroy', $block) }}')"
                                                ><i class="fa-solid fa-times"></i></button>
                                            @endif
                                        @endcan
                                        </span>
                                </li>
                            @empty
                                <li><h4 class="text-center mb-5">{{ __('locations.block.no') }}</h4></li>
                            @endforelse
                        </ul>
                    </div>
                    <div
                            class="tab-pane fade"
                            id="tab-pane-grades" role="tabpanel" aria-labelledby="tab-grades" tabindex="0"
                    >
                        <livewire:subject-matter.learning.grade-translations-editor :campus="$campus"/>
                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        function setIcon() {
            let fl_files = event.target.files; // JS FileList object

            // use the 1st file from the list
            let fl_file = fl_files[0];
            let reader = new FileReader(); // built in API

            let display_file = (e) => {
                $('#icon-container .img-mini').html(e.target.result);
            };

            let on_reader_load = (fl) => {
                return display_file; // a function
            };

            // Closure to capture the file information.
            reader.onload = on_reader_load(fl_file);

            // Read the file as text.
            reader.readAsText(fl_file);
        }

        const sortable = new Draggable.Sortable(document.querySelectorAll('.block-list'), {
            draggable: '.block',
            handle: '.block-move-handle',
            mirror: {
                constrainDimensions: true
            }
        });

        sortable.on('sortable:stop', () => {
            let sorted = [];
            $('.block:not(.draggable--original):not(.draggable-mirror)').each((index, block) => {
                sorted.push($(block).attr('block-id'))
            });

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('locations.blocks.update.order') }}';
            var putInput = document.createElement('input');
            putInput.type = 'hidden';
            putInput.name = '_method';
            putInput.value = 'PUT';
            form.appendChild(putInput);
            var csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = jQuery('meta[name="csrf-token"]').attr('content');
            form.appendChild(csrf);
            var sortInput = document.createElement('input');
            sortInput.type = 'hidden';
            sortInput.name = 'blocks';
            sortInput.value = JSON.stringify(sorted);
            form.appendChild(sortInput);
            document.body.appendChild(form);
            form.submit();
        })
    </script>
@endpush
