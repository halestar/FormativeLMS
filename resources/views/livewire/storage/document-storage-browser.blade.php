<div class="modal-dialog modal-xl">
    @if($open)
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="document-browser-title">{{ __('storage.document.browser') }}</h1>
            </div>
            <div class="modal-body mw-100 position-relative">
                @if(count($tabs) > 0)
                    <ul class="nav nav-tabs">
                        @foreach($tabs as $instance => $tab)
                            <li class="nav-item">
                                <button class="nav-link {{ $selectedTab == $instance? 'active': '' }}"
                                        type="button" wire:click="setTab('{{ $instance }}')">
                                    @if($instance == "upload")
                                        {{ trans_choice('storage.documents.file.upload', ($multiple? 2: 1)) }}
                                    @else
                                        {{ $tab }}
                                    @endif
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    <div class="tab-content">
                        <div
                                class="tab-pane fade show active"
                                tabindex="-1"
                                style="height: 600px;"
                        >
                            @if($allowUpload && $selectedTab == "upload")
                                <div
                                        x-data="{dragging: false}"
                                        x-on:dragenter="dragging = true"
                                        x-on:dragover.prevent="dragging = true"
                                        x-on:dragleave="dragging = false"
                                        x-on:drop="
                                    files = $event.dataTransfer.files;
                                    if(files.length === 1)
                                    {
                                            $wire.upload('uploadedFiles', files[0], (uploadedFilename) => $wire.uploadFiles(),
                                                () => ul_error = true,
                                                (event) => {},
                                                () => {});
                                    }
                                    else if(files.length > 1)
                                    {
                                        $wire.uploadMultiple('uploadedFiles', files, (uploadedFilename) => $wire.uploadFiles(),
                                            () => ul_error = true,
                                            (event) => {},
                                            () => {});
                                    }
                                    dragging = false"
                                        class="overflow-hidden text-center border border-5 border-dashed p-3 m-3 h-100 rounded d-flex"
                                        :class="dragging && 'text-bg-secondary'"

                                >
                                    <div class="m-auto display-2 text-uppercase fw-bold">{{ __('storage.documents.file.drop') }}</div>
                                </div>
                            @else
                                <livewire:storage.document-file-browser
                                        :documentStorage="$documentStorages[$selectedTab]"
                                        :person="$user"
                                        :multiple="$multiple"
                                        :canSelectFolders="$canSelectFolders"
                                        :mimeTypes="$mimeTypes"
                                        wire:key="{{ $selectedTab }}"
                                />
                            @endif
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning d-5" role="alert">
                        {{ __('storage.document.browser.no') }}
                    </div>
                @endif

                <div class="position-absolute bottom-0 end-0 w-100 h-100 text-bg-danger rounded border border-danger opacity-75 d-flex d-none"
                     id="error-alert">
                    <div class="m-auto d-flex justify-content-center align-items-center">
                        <div class="display-1 text-warning pe-3 me-3"><i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="fs-4 text-start w-75" id="error-msg"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div>
                    @if($selectedItems)
                        <span class="text-bg-secondary rounded p-2 m-2 fw-bold">{{ __('storage.documents.file.selected') }}:</span>
                        @if($multiple)
                            @foreach($selectedItems as $selectedItem)
                                <span class="text-bg-light rounded p-2 m-2">
                                <i class="fa-solid fa-file text-info me-2 pe-2 border-end"></i>
                                {{ $selectedItems['name'] }}
                            </span>
                            @endforeach
                        @else
                            <span class="text-bg-light rounded border p-2 m-2">
                                <i class="fa-solid fa-file text-info me-2 pe-2 border-end"></i>
                                {{ $selectedItems->name }}
                            </span>
                        @endif
                    @endif
                </div>
                <div>
                    @if($selectedItems)
                        <button type="button" class="btn btn-primary" wire:click="selectFiles">
                            {{ trans_choice('storage.documents.file.select', is_array($selectedItems)? 2: 1) }}
                        </button>
                    @endif
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
@script
<script>
    $wire.on('document-storage-browser.open-browser-success', () => {
        $('#document-browser-modal').modal('show');
    });

    $wire.on('document-storage-browser.error', (event) => {
        $('#error-msg').html(event.message);
        $('#error-alert').show();
        setTimeout(() => {
            $('#error-alert').addClass('d-none');
        }, 5000);
    });
</script>
@endscript