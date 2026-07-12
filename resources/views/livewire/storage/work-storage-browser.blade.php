<div class="card shadow-sm border-secondary-subtle position-relative"
     style="height: {{ $height }}; max-height: {{ $maxHeight }};">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2 px-3"
         style="height: 50px; max-height: 50px;">
        <div class="marquee-text fw-bold my-auto">
            <span class="fs-5">
                <i class="fa-solid fa-cloud-arrow-up me-2"></i>{{ $title }}
            </span>
        </div>
        <button
                type="button"
                class="btn btn-light btn-sm text-primary fw-bold shadow-sm"
                wire:click="dispatch('document-storage-browser.open-browser',
                    {
                        config:
                            {
                                multiple: true,
                                mimetypes: [],
                                allowUpload: true,
                                canSelectFolders: false,
                                cb_instance: 'work-storage-browser'
                            }
                    });"
                title="Browse Repository"
        ><i class="fa-solid fa-folder-open me-1"></i> Browse
        </button>
    </div>
    <div
            class="card-body overflow-auto p-3 d-flex flex-column"
            style="height: calc(100% - 50px); max-height: calc(100% - 50px); background-color: #f8f9fa;"
            x-data="{ dragging: false, uploading: false, progress: 0, ul_error: false }"
            x-on:dragenter="dragging = true"
            x-on:dragover.prevent="dragging = true"
            x-on:dragleave="dragging = false"
            x-on:drop.prevent="
        files = $event.dataTransfer.files;
        uploading = true;
        if(files.length === 1)
        {
            $wire.upload('uploadedFiles', files[0], (uploadedFilename) => { uploading = false; $wire.uploadFiles(uploadedFilename)},
                (event) => { ul_error = true; console.log(event)},
                (event) => { progress = event.detail.progress },
                () => {});
        }
        else if(files.length > 1)
        {
            $wire.upload('uploadedFiles', files[0], (uploadedFilename) => { uploading = false; $wire.uploadFiles(uploadedFilename)},
                (event) => { ul_error = true; console.log(event)},
                (event) => { progress = event.detail.progress },
                () => {});
        }
        dragging = false"
            :class="dragging ? 'border border-primary border-2 border-dashed bg-primary-subtle' : ''"
    >
        @error('uploadedFiles')
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @enderror
        @if(count($workFiles) == 0)
            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted opacity-75"
                 style="pointer-events: none;">
                <i class="fa-solid fa-file-arrow-up display-1 mb-3"></i>
                <h5 class="fw-normal">{{ __('storage.documents.file.drop') }}</h5>
                <p class="small">Drag and drop files here</p>
            </div>
        @else
            <ul class="list-group shadow-sm mb-auto">
                @foreach($workFiles as $file)
                    <li
                            class="list-group-item d-flex justify-content-between align-items-center p-3 border-start border-4 border-primary"
                            wire:key="work-file-{{ $file->id }}"
                    >
                        <div class="d-flex align-items-center flex-grow-1 text-truncate me-3">
                            <i class="fa-regular fa-file-lines fs-4 text-secondary me-3"></i>
                            <div class="marquee-text text-start fw-medium text-dark">
                                <span>{{ $file->name }}</span>
                            </div>
                        </div>
                        <div class="btn-group btn-group-sm shadow-sm" role="group">
                            @if($showDownload)
                                <a
                                        role="button"
                                        class="btn btn-outline-primary"
                                        href="{{ $file->url }}"
                                        title="Download"
                                ><i class="fa-solid fa-download"></i></a>
                            @endif
                            @if($showLinks)
                                <button
                                        type="button"
                                        class="btn btn-outline-info"
                                        onclick="copyLink($(this), '{{ $file->url }}', {duration: 2000})"
                                        title="Copy Link"
                                ><i class="fa-solid fa-link"></i></button>
                            @endif
                            <button
                                    type="button"
                                    class="btn btn-outline-danger"
                                    wire:click="removeFile('{{ $file->id }}')"
                                    wire:confirm="{{ __('storage.work.file.remove.prompt') }}"
                                    title="Remove"
                            ><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
        <div
                class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex justify-content-center align-items-center flex-column z-3"
                x-show.important="uploading"
        >
            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h4 class="text-primary fw-bold mb-3">{{ __('common.uploading') }}</h4>
            <div class="progress w-75 shadow-sm rounded-pill" role="progressbar" :aria-valuenow="progress"
                 aria-valuemin="0"
                 aria-valuemax="100" style="height: 1.5rem;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                     :style="{ width: progress + '%'}">
                    <span x-text="progress + '%'"></span>
                </div>
            </div>
        </div>
    </div>
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
    window.addEventListener('document-storage-browser-files-selected',
        (event) => (event.detail.cb_instance === 'work-storage-browser') ? $('#work_storage_browser_loading').removeClass('d-none') : null);
</script>
@endscript
