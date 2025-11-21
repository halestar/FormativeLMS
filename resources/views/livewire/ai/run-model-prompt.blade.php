<div>
    <button
        type="button"
        class="{{ $btnClasses }}"
        wire:loading.class="ai-thinking"
        wire:target="showPromptResults,executePrompt"
        wire:click="set('runMode', true)"
    >
        @svg('images/ai-icon.svg', 'img-icon-small')
        <span class="border-start ps-2 ms-2">{{ $propertyName }}</span>
    </button>
    @if($runMode || $resultsMode)
        @if($teleportTo)
            @teleport($teleportTo)
        @endif
        <div class="{{ $classes }}" wire:loading.class="d-none" wire:target="showPromptResults,executePrompt">
            @if($runMode)
                <div class="row mb-3">
                    <div class="col-md-4 d-flex justify-content-center align-items-center flex-column">
                        <h5>{{ __('ai.prompt') }}</h5>
                        <div class="form-check">
                            <a role="button" class="btn btn-sm btn-primary" href="{{ route('ai.prompt.editor', $prompt) }}">
                                {{ __('ai.prompt.editor') }}
                            </a>
                        </div>
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
                <div class="row mb-3">
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary w-100"
                                wire:click="showPromptResults">{{ __('ai.execute') }}</button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-danger w-100"
                                wire:click="executePrompt">{{ __('ai.execute.save') }}</button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-info w-100"
                                wire:click="set('runMode', false)">{{ __('common.cancel') }}</button>
                    </div>
                </div>
            @endif
            @if($resultsMode && $runMode)
                <hr />
            @endif
            @if($resultsMode)
                <h4 class="mb-4">{{ __('ai.prompt.results') }}</h4>
                {!! $results !!}
                <div class="row mt-3">
                    <button type="button" class="btn btn-primary mx-2 col"
                            wire:click="saveModel">{{ __('ai.prompt.results.save') }}</button>
                    <button type="button" class="btn btn-warning mx-2 col"
                            wire:click="showPromptResults">{{ __('ai.prompt.results.again') }}</button>
                    <button type="button" class="btn btn-danger mx-2 col"
                            wire:click="discard">{{ __('ai.prompt.results.discard') }}</button>
                </div>
            @endif
        </div>
        @if($teleportTo)
            @endteleport
        @endif
    @endif
</div>