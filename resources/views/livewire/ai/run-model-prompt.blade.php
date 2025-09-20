<div class="{{ $classes }} border rounded border-dark text-bg-secondary m-1 p-2 position-relative">
    @if($buttonMode)
        <button type="button" class="btn btn-light" wire:click="setMode('runMode')" x-data="{ thinking: false }" x-on:thinking="thinking = true">
            @svg('images/ai-icon.svg', 'img-icon-small')
            <span class="border-start ps-2 ms-2">{{ $model->promptDescription() }}</span>
        </button>
    @elseif($runMode)
        <div class="row mb-3">
            <div class="col-md-4 d-flex justify-content-center align-items-center flex-column">
                <h5>{{ __('ai.prompt') }}</h5>
                <div class="form-check">
                    <input class="form-check-input me-2" type="radio" value="default" id="default_prompt" wire:model="promptType">
                    <label class="form-check-label" for="default_prompt">
                        {{ __('ai.prompt.use') }}
                        @can('system.ai')
                            <a class="ms-2 link-primary link-underline-opacity-0" href="{{ route('ai.prompt.editor', $defaultPrompt) }}"><i class="fa-solid fa-edit"></i></a>
                        @endcan
                    </label>
                </div>
                <div class="text-center my-2 text-uppercase fs-6">
                    &mdash; {{ __('common.or') }} &mdash;
                </div>
                @if($customPrompt)
                    <div class="form-check">
                        <input class="form-check-input me-3" type="radio" value="custom" id="custom_prompt" wire:model="promptType">
                        <label class="form-check-label" for="custom_prompt">
                            {{ __('ai.prompt.custom.use') }}
                            <a class="ms-2 link-primary link-underline-opacity-0" href="{{ route('ai.prompt.editor', $customPrompt) }}"><i class="fa-solid fa-edit"></i></a>
                        </label>
                    </div>
                @else
                    <button type="button" class="btn btn-primary btn-sm" wire:click="createCustomPrompt">{{ __('ai.prompt.custom.create') }}</button>
                @endif
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label" for="ai_connection">{{ __('ai.system') }}</label>
                    <select id="ai_connection" class="form-select" wire:model="selectedAiId">
                        @foreach($aiConnections as $conn)
                            <option value="{{ $conn->id }}">{{ $conn->service->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label" for="ai_model">{{ __('ai.model') }}</label>
                    <select id="ai_model" class="form-select" wire:model="selectedLlm">
                        @foreach($Llms as $Llm)
                            <option value="{{ $Llm }}">{{ $Llm }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <button type="button" class="btn btn-primary w-100" wire:click="showPromptResults">{{ __('ai.execute') }}</button>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-danger w-100" wire:click="executePrompt">{{ __('ai.execute.save') }}</button>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-info w-100" wire:click="setMode('buttonMode')">{{ __('common.cancel') }}</button>
            </div>
        </div>
    @elseif($resultsMode)
        <h4 class="mb-4">{{ __('ai.prompt.results') }}</h4>
        {!! $results !!}
        <div class="row mt-3">
            <button type="button" class="btn btn-primary mx-2 col" wire:click="saveModel">{{ __('ai.prompt.results.save') }}</button>
            <button type="button" class="btn btn-warning mx-2 col" wire:click="showPromptResults">{{ __('ai.prompt.results.again') }}</button>
            <button type="button" class="btn btn-danger mx-2 col" wire:click="discard">{{ __('ai.prompt.results.discard') }}</button>
        </div>
    @endif
    <div class="position-absolute top-0 end-0 w-100 h-100 d-flex justify-content-center d-none" wire:loading.class.remove="d-none" wire:target="showPromptResults,executePrompt">
        <div class="ai-thinking my-auto h-100 w-100">
            @svg('images/ai-icon.svg', 'h-75 w-100')
            <div class="mt-1 fw-bold text-uppercase fs-5">{{ __('ai.thinking') }}</div>
        </div>
    </div>
</div>