<div class="mw-100 mh-100 overflow-hidden p-3 position-relative h-100">
    <h5 class="card-header d-flex justify-content-start align-items-center">
        <button class="btn btn-outline-light" wire:click="addFolder()">
            <i class="fa-solid fa-folder-plus text-warning"></i>
        </button>
        <div class="flex-grow-1 me-2">
            <div class="input-group">
                <input
                        type="text"
                        id="assets-filter-term"
                        class="form-control border-end-0 border rounded-start-pill"
                        placeholder="{{ __('common.search') }}"
                        wire:model.live.debounce="filterTerms"
                />
                <button
                        class="btr btn-outline-secondary bg-white border-start-0 border rounded-end-pill pe-3"
                        wire:click="clearFiter()"
                >
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    </h5>

    <div
            x-data="{asset_dragging: false, asset_path: '', external_dragging: false, ul_error: false, ul_progress: -1, ul_success: false}"
            x-on:dragenter="external_dragging = !asset_dragging"
            x-on:dragover.prevent="external_dragging = !asset_dragging"
            x-on:dragend="external_dragging = false"
            class="overflow-hidden mt-3 h-100"
            id="asset-viewer"
    >
        <div class="row row-cols-6 mh-100">
            @if($selectedFolder)
                <div class="col mb-3">
                    <div
                            x-data="{dropping: false}"
                            wire:key="{{ $selectedFolder->safeName() }}"
                            class="card asset-folder border-0 overflow-hidden"
                            :class="dropping && 'asset-drop'"
                            draggable="false"
                            x-on:dblclick='$wire.viewParent("{{ $selectedFolder->path }}")'
                            style="height: 100px;"
                            path="{{ $documentStorage->parentDirectory($person, $selectedFolder)?->path }}"
                            x-on:dragenter="dropping = asset_dragging"
                            x-on:dragover.prevent="dropping = asset_dragging && asset_path !== $el.getAttribute('path')"
                            x-on:dragleave="dropping = false"
                            x-on:drop.prevent="
                            if(asset_dragging && asset_path !== $el.getAttribute('path'))
                            {
                                $wire.moveToFolder(asset_path, $el.getAttribute('path'));
                                asset_dragging = false;
                                dropping = false;
                            }"
                    >
                    <span class="m-auto fa-stack fa-2x">
                        <i class="fa-solid fa-folder fa-stack-2x text-warning"></i>
                        <i class="fa-solid fa-turn-up fa-stack-1x"></i>
                    </span>
                    </div>
                    <div class="border-bottom border-start border-end rounded-bottom py-1 px-0 text-bg-light text-center">
                        <h1>..</h1>
                    </div>
                </div>
            @endif
            @foreach($assets as $asset)
                <div class="col mb-3">
                    @if($asset->isFolder)
                        <div
                                x-data="{dropping: false}"
                                wire:key="{{ $asset->safeName() }}"
                                class="card asset-folder overflow-hidden show-as-action @if($this->isSelected($asset)) bg-secondary border-secondary @else border-0 @endif"
                                :class="dropping && 'asset-drop'"
                                draggable="true"
                                x-on:dblclick='$wire.viewFolder("{{ $asset->path }}")'
                                @if($canSelectFolders)
                                    wire:click="selectFile('{{ $asset->path }}')"
                                @endif
                                style="height: 100px;"
                                path="{{ $asset->path }}"
                                x-on:dragstart="asset_dragging = true;
                                                asset_path = $el.getAttribute('path');"
                                x-on:dragend="asset_dragging = false; asset_path = '';"
                                x-on:dragenter="dropping = asset_dragging && asset_path !== $el.getAttribute('path')"
                                x-on:dragover.prevent="dropping = asset_dragging && asset_path !== $el.getAttribute('path')"
                                x-on:dragleave="dropping = false"
                                x-on:drop.prevent="
                                if(asset_dragging && asset_path !== $el.getAttribute('path'))
                                {
                                    $wire.moveToFolder(asset_path, $el.getAttribute('path'));
                                    asset_dragging = false;
                                    dropping = false;
                                }"

                        >
                            <span class="display-3 m-auto">{!! $asset->icon !!}</span>
                            <div class="card-img-overlay">
                                <div class="d-flex justify-content-end">
                                    @if($asset->canDelete)
                                        <button
                                                type="button"
                                                class="btn btn-outline-danger btn-sm rounded"
                                                wire:click="removeDataItem('{{ $asset->path }}')"
                                                wire:confirm="{{ __('storage.document.folder.delete.confirm') }}"
                                        ><i class="fa fa-times"></i></button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom border-start border-end rounded-bottom py-1 px-0 text-bg-light">
                            <div
                                    class="row align-items-center w-100 p-0 m-0"
                                    id="span-{{ $asset->safeName() }}"
                            >
                                <div
                                        class="text-wrap fs-6 col-12"
                                        @if($asset->canChangeName)
                                            onclick="$('#span-{{ $asset->safeName() }}').hide();$('#name-{{ $asset->safeName() }}').removeClass('d-none');"
                                        @endif
                                >
                                    {{ $asset->name }}
                                </div>
                            </div>
                            @if($asset->canChangeName)
                                <div
                                        class="input-group d-none"
                                        id="name-{{ $asset->safeName() }}"
                                >
                                    <input
                                            type="text"
                                            value="{{ $asset->name }}"
                                            id="input-{{ $asset->safeName() }}"
                                            class="form-control form-control-sm"
                                    />
                                    <button
                                            type="button"
                                            class="btn btn-sm btn-success"
                                            wire:click="updateName('{{ $asset->path }}', $('#input-{{ $asset->safeName() }}').val())"
                                    ><i class="fa fa-check"></i></button>
                                    <button
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            onclick="$('#span-{{ $asset->safeName() }}').show();$('#name-{{ $asset->safeName() }}').addClass('d-none');"
                                    ><i class="fa fa-times"></i></button>
                                </div>
                            @endif
                        </div>
                    @else
                        <div
                                wire:key="{{ $asset->safeName() }}"
                                class="card asset-file rounded-bottom-0 overflow-hidden show-as-action @if($this->isSelected($asset)) bg-secondary border-secondary @endif"
                                draggable="true"
                                style="height: 100px;"
                                wire:click="selectFile('{{ $asset->path }}')"
                                path="{{ $asset->path }}"
                                x-on:dragstart="asset_dragging = true;
                                                asset_path = $el.getAttribute('path');"
                                x-on:dragend="asset_dragging = false; asset_path = '';"
                        >
                            <span class="display-3 m-auto">{!! $asset->icon !!}</span>
                            <div class="card-img-overlay">
                                <div class="d-flex justify-content-between">
                                    @if($asset->canPreview)
                                        <button
                                                type="button"
                                                class="btn btn-outline-primary btn-sm rounded"
                                                wire:click="viewFile('{{ $asset->path }}')"
                                        ><i class="fa fa-search"></i></button>
                                    @endif
                                    @if($asset->canDelete)
                                        <button
                                                type="button"
                                                class="btn btn-outline-danger btn-sm rounded"
                                                wire:click="removeFile('{{ $asset->path }}')"
                                                wire:confirm="{{ __('storage.document.file.delete.confirm') }}"
                                        ><i class="fa fa-times"></i></button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom border-start border-end rounded-bottom py-1 px-0 text-bg-light">
                            <div
                                    class="row align-items-center w-100 p-0 m-0"
                                    id="span-{{ $asset->safeName() }}"
                            >
                                <div
                                        class="text-break fs-6 col-12"
                                        @if($asset->canChangeName)
                                            onclick="$('#span-{{ $asset->safeName() }}').hide();$('#name-{{ $asset->safeName() }}').removeClass('d-none');"
                                        @endif
                                >
                                    {{ $asset->name }}
                                </div>
                            </div>
                            @if($asset->canChangeName)
                                <div
                                        class="input-group d-none"
                                        id="name-{{ $asset->safeName() }}"
                                >
                                    <input
                                            type="text"
                                            value="{{ $asset->name }}"
                                            id="input-{{ $asset->safeName() }}"
                                            class="form-control form-control-sm"
                                    />
                                    <button
                                            type="button"
                                            class="btn btn-sm btn-success"
                                            wire:click="updateName('{{ $asset->path }}', $('#input-{{ $asset->safeName() }}').val())"
                                    ><i class="fa fa-check"></i></button>
                                    <button
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            onclick="$('#span-{{ $asset->safeName() }}').show();$('#name-{{ $asset->safeName() }}').addClass('d-none');"
                                    ><i class="fa fa-times"></i></button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <div
                x-on:dragenter="external_dragging = !asset_dragging"
                x-on:dragover.prevent="external_dragging = !asset_dragging"
                x-on:dragleave="external_dragging = false"
                x-on:drop="
                    files = $event.dataTransfer.files;
                    if(files.length === 1)
                    {
                            $wire.upload('uploads', files[0], (uploadedFilename) => $wire.addFile(),
                                () => ul_error = true,
                                (event) => ul_progress = (event.detail.progress == 0 || event.detail.progress == 100) ? -1 : event.detail.progress,
                                () => {});
                    }
                    else if(files.length > 1)
                    {
                        $wire.uploadMultiple('uploads', files, (uploadedFilename) => $wire.addFile(),
                            () => ul_error = true,
                            (event) => ul_progress = (event.detail.progress == 0 || event.detail.progress == 100) ? -1 : event.detail.progress,
                            () => {});
                    }
                    external_dragging = false"
                class="position-absolute top-0 start-0 w-100 h-100 text-bg-secondary opacity-75 d-flex justify-content-center align-items-center"
                :class="external_dragging || 'd-none'"
                id="ul-overlay"
        >
            <span
                    class="display-2"
                    x-on:dragenter="external_dragging = true"
                    x-on:dragover.prevent="external_dragging = true"
            >{{ __('storage.document.asset.drop') }}</span>
        </div>

    </div>

    @if($viewingFile)
        <div class="position-fixed top-0 start-0 w-100 h-100 text-bg-dark d-flex justify-content-center align-items-center z-2 opacity-75">
            <button
                    type="button"
                    class="btn btn-danger position-fixed"
                    style="top: 10%; right: 10%;"
                    wire:click="closeView"
            ><i class="fa fa-times"></i></button>
            <div class="card z-3">
                {!! $viewingFile->preview() !!}
            </div>
        </div>
    @endif
</div>
@script
<script>
    // prevent all the defaults first
    window.addEventListener("dragover", function (e) {
        e = e || event;
        e.preventDefault();
    }, false);
    window.addEventListener("drop", function (e) {
        e = e || event;
        e.preventDefault();
    }, false);
</script>
@endscript