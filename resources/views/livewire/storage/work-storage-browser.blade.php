<div class="card text-bg-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>{{ $title }}</h5>
        <button
                type="button"
                class="btn btn-secondary"
                wire:click="dispatch('document-storage-browser.open-browser',
                    {
                        config:
                            {
                                multiple: true,
                                mimeTypes: [],
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
            x-data="{ dragging: false }"
            x-on:dragenter="dragging = true"
            x-on:dragover.prevent="dragging = true"
            x-on:dragleave="dragging = false"
            x-on:drop.prevent="
        files = $event.dataTransfer.files;
        if(files.length === 1)
        {
            $wire.upload('uploadedFiles', files[0], (uploadedFilename) => $wire.uploadFiles(uploadedFilename),
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
            :class="dragging && 'text-bg-info'"
    >
        @if(count($workFiles) == 0)
            <div class="display-4 text-center">
                {{ __('storage.documents.file.drop') }}
            </div>
        @else
        <ul class="list-group list-group-flush">
            @foreach($workFiles as $file)
                <li class="list-group-item list-group-item-primary d-flex justify-content-between align-items-center">
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
</script>
@endscript
