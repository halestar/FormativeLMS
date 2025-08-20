<div>
    @if($editing)
        <h5>{{ ($className)::prettyName() }}</h5>
        <form wire:submit="updateLmsStorage">
            <div class="row mb-3">
                <div class="col">
                    <label for="instanceProperty_{{ $instanceProperty }}"
                           class="form-label">{{ ($className)::instancePropertyName() }}</label>
                    <input
                            type="text"
                            id="instanceProperty_{{ $instanceProperty }}"
                            wire:model="instanceProperty"
                            class="form-control @error('instanceProperty') is-invalid @enderror"
                            @if($lmsStorage)
                                readonly
                            disabled
                            @endif
                            aria-describedby="instanceProperty_{{ $instanceProperty }}_help"
                    />
                    <x-error-display key="instanceProperty">{{ $errors->first('instanceProperty') }}</x-error-display>
                    <div id="instanceProperty_{{ $instanceProperty }}_help"
                         class="form-text">{{ ($className)::instancePropertyNameHelp() }}</div>
                </div>
                <div class="col">
                    <label for="display-name" class="form-label">{{ __('settings.storage.display') }}</label>
                    <input
                            type="text"
                            id="display-name"
                            wire:model="displayName"
                            class="form-control @error('displayName') is-invalid @enderror"
                    />
                    <x-error-display key="displayName">{{ $errors->first('displayName') }}</x-error-display>
                    <div id="display-name-help" class="form-text">{{ __('settings.storage.display.help') }}</div>
                </div>
            </div>
            <!-- Add extra options here -->
            <div class="row">
                <button type="submit" class="col btn btn-primary mx-2">
                    {{ __('common.apply') }}
                </button>
                <button
                        type="button"
                        class="col btn btn-danger mx-2"
                        @if($lmsStorage)
                            @click="$wire.set('editing', false)"
                        @else
                            @click="$wire.dispatch('lms-storage-instance.remove-uninstantiated-lms-storage', {className: '{{ $className }}'})"
                        @endif
                >
                    {{ __('common.cancel') }}
                </button>
            </div>
        </form>
    @else
        <div class="d-flex justify-content-between align-items-center">
            <h5>{{ $lmsStorage->displayName }} ({{$lmsStorage::prettyName()}})</h5>
            <div>
                <button type="button" class="btn btn-primary mx-2"
                        @click="$wire.set('editing', true)">{{ __('common.edit') }}</button>
                <button type="button" class="btn btn-danger mx-2"
                        @click="$wire.dispatch('lms-storage-instance.remove-lms-storage', {instanceProperty: '{{ $lmsStorage->instanceProperty }}'})"
                >{{ __('common.delete') }}</button>
            </div>
        </div>
    @endif
</div>
