<div>
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-8">
                <div class="mb-3 d-flex justify-content-between align-content-center">
                    <select class="form-select w-50" wire:model="promptType" wire:change="setPromptType">
                        <option value="prompt">{{ __('ai.prompt') }}</option>
                        <option value="system">{{ __('ai.prompt.system') }}</option>
                    </select>
                    <button type="button" class="btn btn-warning" wire:click="resetPrompt">{{ __('ai.prompt.reset') }}</button>
                </div>
                <div class="alert alert-info mb-3">{{ __('ai.prompt.description') }}</div>
                <livewire:utilities.text-editor
                        wire:model.live.debounce="prompt"
                        :fileable="$aiPrompt"
                        :key="$reloadKey"
                ></livewire:utilities.text-editor>
            </div>
            <div class="col-md-4">
                <div x-data="{ temperature: $wire.entangle('temperature') }">
                    <div class="alert alert-info mb-3">{{ __('ai.prompt.temperature.description') }}</div>
                    <label for="temperature" class="form-label">{{ __('ai.prompt.temperature') }}</label>
                    <input type="range" class="form-range" min="0" max="2" step="0.01" id="temperature"
                           wire:model="temperature">
                    <div class="fw-bold mb-3">
                        <span class="text-primary"
                              x-show="temperature <= 0.35">{{ __('ai.prompt.temperature.low') }}</span>
                        <span class="text-warning"
                              x-show="temperature > 0.35 && temperature <= 0.7 ">{{ __('ai.prompt.temperature.med') }}</span>
                        <span class="text-danger"
                              x-show="temperature > 0.7 && temperature <= 1">{{ __('ai.prompt.temperature.high') }}</span>
                        <span class="text-multi-color"
                              x-show="temperature > 1">{{ __('ai.prompt.temperature.eleven') }}</span>
                    </div>
                </div>

                <div class="alert alert-info mb-3">{{ __('ai.prompt.work.description') }}</div>
                <livewire:storage.work-storage-browser :fileable="$aiPrompt" title="{{ __('ai.prompt.work') }}"/>
            </div>
        </div>
        <div class="row">
            <button type="button" wire:click="updatePrompt" class="col btn btn-primary mx-2" id="update_button">{{ __('common.update') }}</button>
            <button type="button" wire:click="revert" class="col btn btn-warning mx-2">{{ __('common.revert') }}</button>
        </div>
    </div>
</div>
@script
<script>
    $wire.on('saved', () => {
        setTimeout(() => {
            $('#update_button').removeClass('btn-primary').addClass('btn-success').html('{{ __('common.saved') }}');
            setTimeout(() => {
                $('#update_button').addClass('btn-primary').removeClass('btn-success').html('{{ __('common.update') }}');
            }, 5000)
        }, 100);
    })
</script>
@endscript
