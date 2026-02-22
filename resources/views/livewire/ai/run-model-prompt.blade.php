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
        <div class="{{ $classes }}" style="{!! $style !!}" wire:loading.class="d-none" wire:target="showPromptResults,executePrompt">
            @if($runMode)
                <div class="row mb-3" x-data="{ expanded: false }">
                    <div class="col-md-8">
                        <div class="alert alert-info position-relative">
                            <div class="alert-heading d-flex align-items-center justify-content-between">
                                <span class="align-self-center">{{ __('ai.prompt') }}</span>
                                <a role="button" href="{{ route('ai.prompt.editor', $prompt) }}">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </div>
                            <div
                                class="mt-2 bg-light border rounded p-2 small overflow-hidden "
                                :style="!expanded && 'height: 75px !important;'"
                            >
                                {!! $prompt->renderPrompt($model); !!}
                            </div>
                            <span class="position-absolute bottom-0 start-50 translate-middle-x text-primary" @click="expanded = !expanded">
                                <i class="bi" :class="expanded? 'bi-arrow-up-circle-fill': 'bi-arrow-down-circle-fill'"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="ai_connection">{{ __('ai.system') }}</label>
                            <select id="ai_connection" class="form-select" wire:model="selectedLlmId">
                                @foreach($llms as $llm)
                                    <option value="{{ $llm->id }}" @selected($llm->id == $selectedLlmId)>
                                        @if($llm->provider->isSystem())
                                            {{ __('system.settings.ai.llm.system') }}
                                        @else
                                            {{ __('system.settings.ai.llm.personal') }}
                                        @endif
                                        : {{ $llm->name }}
                                    </option>
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
