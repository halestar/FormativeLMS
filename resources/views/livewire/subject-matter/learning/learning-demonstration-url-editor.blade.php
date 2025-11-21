<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="card-title">{{ __('learning.demonstrations.resources') }}</div>
            @if($canUseAI)
                <livewire:ai.run-model-prompt
                        :model="$ld" property="links"
                        classes="m-2 p-2 border rounded text-bg-light"
                        btn-classes="btn btn-sm btn-light border"
                        teleport-to="#links-ai-results"
                />
            @endif
            <button
                type="button"
                class="btn btn-sm btn-primary"
                wire:click="addResource"
            >{{ __('learning.demonstrations.resources.add') }}</button>
    </div>
    <div class="card-body">
        <div id="links-ai-results"></div>
        <ul class="list-group list-group-flush">
            @foreach($resources as $resource)
                <li class="list-group-item d-flex justify-content-between align-items-center" wire:key="link-{{$loop->index}}">
                    <div class="me-3 flex-grow-1">
                        <div class="input-group mb-3">
                            <span class="input-group-text">{{ __('learning.demonstrations.resources.title') }}:</span>
                            <input
                                type="text"
                                class="form-control"
                                wire:blur="updateTitle({{$loop->index}}, $event.target.value)"
                                value="{{ $resource['title'] }}"
                            />
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">{{ __('learning.demonstrations.resources.url') }}:</span>
                            <input
                                type="text"
                                class="form-control"
                                wire:blur="updateUrl({{$loop->index}}, $event.target.value)"
                                value="{{ $resource['url'] }}"
                            />
                        </div>
                    </div>
                    <div class="ps-2 border-start">
                        <button
                            type="button"
                            class="btn btn-sm btn-danger"
                            wire:click="removeResource({{$loop->index}})"
                        ><i class="fa-solid fa-times"></i></button>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
