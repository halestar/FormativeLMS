<div class="card text-bg-light">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>{{ $title }}</h4>
        @if(!$saved)
            <div>
                <button type="button" class="btn btn-primary mx-2" wire:click="save()">{{ __('common.apply') }}</button>
                <button type="button" class="btn btn-danger mx-2"
                        wire:click="revert()">{{ __('common.revert') }}</button>
            </div>
        @endif
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @foreach($documentStorages as $dStorage)
                <li class="list-group-item list-group-item-light">
                    <livewire:storage.lms-storage-instance :lStorage="$dStorage"
                                                           :key="is_string($dStorage) ? $dStorage : $dStorage->instanceProperty"/>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="card-footer">
        @foreach($storageSettings->document_storages as $storage)
            <button class="btn btn-primary"
                    wire:click="addDocumentStorage('{!! str_replace('\\', '\\\\', $storage) !!}')">
                <i class="fa fa-plus border-end me-2 pe-2"></i>
                {{ $storage::prettyName() }}
            </button>
        @endforeach
    </div>
</div>
