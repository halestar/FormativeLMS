<div class="card text-bg-primary position-relative" style="height: {{ $height }}; max-height: {{ $maxHeight }};">
    <div class="card-header d-flex justify-content-between align-items-center p-1" style="height: 45px; max-height: 45px;">
        <h6 class="marquee-text fw-bold my-auto">
            <span>
            {{ $title }}
            </span>
        </h6>
        <button
                type="button"
                class="btn btn-secondary btn-sm ms-3"
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
        ><i class="fa-solid fa-folder-open"></i></button>
    </div>
    <div
            class="card-body overflow-auto p-0"
            style="height: calc(100% - 45px); max-height: calc(100% - 45px);"
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
            :class="dragging && 'text-bg-info'"
    >
        @error('uploadedFiles')
        <div class="alert alert-danger">
            {{ $message }}
        </div>
        @enderror
        @if(count($workFiles) == 0)
            <div class="display-4 text-center">
                {{ __('storage.documents.file.drop') }}
            </div>
        @else
            <ul class="list-group list-group-flush">
                @foreach($workFiles as $file)
                    <li
                        class="list-group-item list-group-item-primary d-flex justify-content-between align-items-center p-2 @if($loop->first) rounded-top @endif @if($loop->last) rounded-bottom @endif"
                        wire:key="work-file-{{ $file->id }}"
                    >
                        <div class="flex-grow-1 text-truncate marquee-text text-start">
                            <span>
                                {{ $file->name }}
                            </span>
                        </div>
                        <div class="btn-group btn-group-sm ms-3" role="group">
                            @if($showDownload)
                                <a
                                    role="button"
                                    class="btn btn-primary"
                                    href="{{ $file->url }}"
                                ><i class="fa fa-download"></i></a>
                            @endif
                            @if($showLinks)
                                <button
                                    type="button"
                                    class="btn btn-info"
                                    onclick="copyLink($(this), '{{ $file->url }}', {duration: 2000})"
                                ><i class="fa fa-link"></i></button>
                            @endif
                            <button
                                    type="button"
                                    class="btn btn-danger"
                                    wire:click="removeFile('{{ $file->id }}')"
                                    wire:confirm="{{ __('storage.work.file.remove.prompt') }}"
                            ><i class="fa fa-times"></i></button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
        <div
                class="position-absolute top-0 start-0 w-100 h-100 text-bg-secondary opacity-60 d-flex justify-content-center align-items-center flex-column"
                x-show.important="uploading"
        >
            <div class="display-5 mb-3">{{ __('common.uploading') }}</div>
            <div class="progress w-50 border border-dark" role="progressbar" :aria-valuenow="progress" aria-valuemin="0"
                 aria-valuemax="100" style="height: 3em;">
                <div class="progress-bar progress-bar-striped" :style="{ width: progress + '%'}"></div>
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
    window.addEventListener('document-storage-browser.files-selected',
        (event) => (event.detail.cb_instance === 'work-storage-browser') ? $('#work_storage_browser_loading').removeClass('d-none') : null);
</script>
@endscript
