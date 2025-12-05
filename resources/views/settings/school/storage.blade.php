@inject('storageSettings','App\Classes\Settings\StorageSettings')
<div class="row mt-3">
    <div class="col-md-4">
        <form action="{{ route('settings.school.update.storage') }}" method="post">
            @csrf
            @method('PATCH')
            <div class="card">
                <h3 class="card-header">{{ __('settings.storage.work') }}</h3>
                <div class="card-body">
                    @foreach(\App\Enums\WorkStoragesInstances::cases() as $storage)
                        <div class="mb-3">
                            <label class="form-label" for="{{ $storage }}">{{ $storage->label() }}</label>
                            <select class="form-select @error($storage->value) is-invalid @enderror"
                                    name="{{ $storage }}"
                                    id="{{ $storage }}">
                                <option value="">{{ __('settings.storage.work.none') }}</option>
                                @foreach($workConnections as $connection)
                                    <option
                                            value="{{ $connection->id }}"
                                            @selected($storageSettings->work_storages[$storage->value] == $connection->id)
                                    >{{ $connection->service->name }}</option>
                                @endforeach
                            </select>
                            <x-utilities.error-display
                                    key="{{ $storage->value }}">{{ $errors->first($storage->value) }}</x-utilities.error-display>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer ">
                    <div class="row justify-content-center">
                        <button type="submit"
                                class="btn btn-primary col">{{ __('settings.storage.work.update') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-8">
        <livewire:storage.mime-configuration/>
    </div>
</div>