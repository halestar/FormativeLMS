<div
        class="card text-bg-primary position-relative"

>
    <div
            class="card-header d-flex justify-content-between align-items-center"
    >
        <h5>{{ $title }}</h5>
        <button
                type="button"
                class="btn btn-secondary"
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
            class="card-body"
            style="min-height: 400px;"
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
        <div class=""
        @if(count($workFiles) == 0)
            <div class="display-4 text-center">
                {{ __('storage.documents.file.drop') }}
            </div>
        @else
            <ul class="list-group list-group-flush">
                @foreach($workFiles as $file)
                    <li class="list-group-item list-group-item-primary d-flex justify-content-between align-items-center" wire:key="work-file-{{ $file->id }}">
                        <div class="flex-grow-1 text-truncate">{{ $file->name }}</div>
                        <button
                                type="button"
                                class="btn btn-danger"
                                wire:click="removeFile('{{ $file->id }}')"
                                wire:confirm="{{ __('storage.work.file.remove.prompt') }}"
                        ><i class="fa fa-times"></i></button>
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
