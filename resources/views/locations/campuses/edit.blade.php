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
                              method="POST">
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
                                                <x-error-display
                                                        key="name">{{ $errors->first('name') }}</x-error-display>
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
                                                <x-error-display
                                                        key="abbr">{{ $errors->first('abbr') }}</x-error-display>
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
                            id="tab-pane-blocks" role="tabpanel" aria-labelledby="tab-blocks" tabindex="0"
                    >
                        <ul class="list-group block-list">
                            @forelse($campus->blocks()->active()->get() as $block)
                                <li class="list-group-item d-flex justify-content-between align-items-end block"
                                    block-id="{{ $block->id }}">
                                    <div class="col-4 @if(!$block->active) text-warning @endif">
                                        <span class="block-move-handle me-2"><i
                                                    class="fa-solid fa-grip-lines-vertical"></i></span>
                                        {{ $block->name }}
                                    </div>
                                    <span>
                                            @can('edit', $block)
                                            <a
                                                    href="{{ route('locations.blocks.edit', $block) }}"
                                                    class="text-decoration-none"
                                            >
                                                {{ $block->periods->implode('abbr', ', ') }}
                                            </a>
                                        @else
                                            {{ $block->periods->implode('abbr', ', ') }}
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
