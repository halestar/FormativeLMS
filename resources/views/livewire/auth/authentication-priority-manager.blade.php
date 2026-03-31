<div>
    @inject('manager', 'App\Classes\Integrators\IntegrationsManager')
    @if($editing)
        <div class="border-bottom d-flex justify-content-between align-items-center pb-2 mb-3">
            <div class="fw-semibold">{{ __('settings.auth.priorities') }}</div>
            <button
                    type="button"
                    class="btn btn-outline-danger btn-sm"
                    x-on:click="$wire.set('editing', false)"
            >{{ __('common.cancel') }}</button>
        </div>
        <ul class="list-group list-group-flush mb-3">
            <li class="list-group-item px-0 py-2">
                <div class="d-flex flex-column gap-2">
                    <div class="fw-semibold small text-uppercase text-muted">
                        {{ __('settings.auth.priorities.default') }}
                    </div>
                    <div class="small text-muted">{{ __('settings.auth.priorities.default.help') }}</div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">{{ __('settings.auth.priorities.modules.use') }}</span>
                        <select
                            class="form-select form-select-sm"
                            id="default_priority_action"
                            wire:model="priorities.0.auth"
                            wire:change="$wire.set('changed', true)"
                        >
                            <option value="">{{ __('auth.block') }}</option>
                            @foreach($manager->getAvailableServices(\App\Enums\IntegratorServiceTypes::AUTHENTICATION) as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                            <option value="choose">{{ __('settings.auth.priorities.modules.choose') }}</option>
                        </select>
                    </div>
                    <div class="alert alert-info py-2 px-2 mb-0" x-show="$wire.priorities[0].auth == 'choose'">
                        <div class="row row-cols-2 row-cols-md-3 g-2">
                            @foreach($manager->getAvailableServices(\App\Enums\IntegratorServiceTypes::AUTHENTICATION) as $service)
                                <div class="form-check col small">
                                    <input
                                            type="checkbox"
                                            class="form-check-input"
                                            wire:model="priorities.0.choices"
                                            id="service_ids_0_{{ $service->id }}"
                                            value="{{ $service->id }}"
                                            wire:click="$wire.set('changed', true)"
                                    >
                                    <label class="form-check-label"
                                           for="service_ids_0_{{ $service->id }}">
                                        {{ $service->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
            <div class="fw-semibold small text-uppercase text-muted">{{ __('settings.auth.priorities.additional') }}</div>
            <button type="button" class="btn btn-primary btn-sm" wire:click="addPriority()">
                <span class="border-end pe-2 me-2"><i class="fa fa-plus"></i></span>{{ __('settings.auth.priority') }}
            </button>
        </div>
        <ul wire:sort="reorderPriorities" class="list-group list-group-flush">
            @foreach($priorities as $priorityId => $priority)
                @continue($priorityId == \App\Classes\Auth\AuthenticationDesignation::DEFAULT_PRIORITY)
                <li wire:sort:item="{{ $priorityId }}" wire:key="{{ $priorityId }}"
                    class="list-group-item px-0 py-2">
                    <div class="d-flex align-items-start gap-2">
                        <div class="pt-1">
                            <button type="button" wire:sort:handle class="btn btn-link p-0 text-muted">
                                <i class="fa-solid fa-grip-lines-vertical"></i>
                            </button>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small text-uppercase text-muted mb-1">
                                {{ __('settings.auth.priorities.number') .  $priorityId }}
                            </div>
                            <div class="row g-2">
                                <livewire:auth.multi-role-selector wire:model="priorities.{{ $priorityId }}.roles" classes="col" />
                                <div class="col">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">{{ __('settings.auth.priorities.modules.use') }}</span>
                                        <select
                                                class="form-select form-select-sm"
                                                wire:model="priorities.{{ $priorityId }}.auth"
                                                wire:click="$wire.set('changed', true)"
                                        >
                                            <option value="">{{ __('auth.block') }}</option>
                                            @foreach($manager->getAvailableServices(\App\Enums\IntegratorServiceTypes::AUTHENTICATION) as $service)
                                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                                            @endforeach
                                            <option value="choose">{{ __('settings.auth.priorities.modules.choose') }}</option>
                                        </select>
                                    </div>
                                    <div class="alert alert-info py-2 px-2 mb-0" x-show="$wire.priorities[{{ $priorityId }}].auth == 'choose'">
                                        <div class="row row-cols-2 row-cols-md-3 g-2">
                                            @foreach($manager->getAvailableServices(\App\Enums\IntegratorServiceTypes::AUTHENTICATION) as $service)
                                                <div class="form-check col small">
                                                    <input
                                                            type="checkbox"
                                                            class="form-check-input"
                                                            id="service_ids_{{ $priorityId }}_{{ $service->id }}"
                                                            value="{{ $service->id }}"
                                                            wire:model="priorities.{{ $priorityId }}.choices"
                                                    >
                                                    <label class="form-check-label"
                                                           for="service_ids_{{ $priorityId }}_{{ $service->id }}">
                                                        {{ $service->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pt-1">
                            <button
                                    type="button"
                                    class="btn btn-link p-0 text-danger"
                                    wire:click="removePriority({{ $priorityId }})"
                                    aria-label="{{ __('crud.remove') }}"
                            ><i class="fa-solid fa-times"></i></button>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
        @if($changed)
            <div class="alert alert-danger py-2 px-2 mt-3 mb-0">
                <div class="fw-semibold small text-uppercase mb-1">{{ __('settings.auth.priorities.warning.heading') }}</div>
                <div class="small mb-2">{!! __('settings.auth.priorities.warning') !!}</div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-danger btn-sm"
                            wire:click="applyChanges">{{ __('common.apply.changes') }}</button>
                    <button type="button" class="btn btn-secondary btn-sm"
                            wire:click="revertChanges">{{ __('common.revert.changes') }}</button>
                </div>
            </div>
        @endif

    @else
        <div class="border-bottom d-flex justify-content-between align-items-center pb-2 mb-3">
            <div class="fw-semibold">{{ __('settings.auth.priorities') }}</div>
            <button
                    type="button"
                    class="btn btn-secondary btn-sm"
                    x-on:click="$wire.set('editing', true)"
            >{{ __('common.edit') }}</button>
        </div>
        @foreach($authSettings->priorities as $priority)
            @continue($priority->priority == \App\Classes\Auth\AuthenticationDesignation::DEFAULT_PRIORITY)
            <div class="alert alert-info py-2 px-2 mb-2">
                <div class="fw-semibold small">
                    {{ __('settings.auth.priorities.number') .  $priority->priority }}:
                    {{ __('settings.auth.priorities.description', ['roles' => implode(', ', $priority->roles)]) }}
                </div>
                <div class="small text-muted">
                    @if($priority->isChoice())
                        [ {{ __('settings.auth.priorities.modules.description.choose', ['modules' => $priority->prettyServices()]) }} ]
                    @elseif($priority->isBlocked())
                        [ {{ __('settings.auth.priorities.modules.description.blocked') }} ]
                    @else
                        [ {{ __('settings.auth.priorities.modules.description', ['module' => $priority->prettyServices()]) }} ]
                    @endif
                </div>
            </div>
        @endforeach
        <div class="alert alert-primary py-2 px-2 mb-0">
            <div class="fw-semibold small">
                {{ __('settings.auth.priorities.default') }}: {{ __('settings.auth.priorities.default.help') }}
            </div>
            <div class="small text-muted">
                @if($authSettings->priorities[0]->isChoice())
                    [ {{ __('settings.auth.priorities.modules.description.choose', ['modules' => $authSettings->priorities[0]->prettyServices()]) }} ]
                @elseif($authSettings->priorities[0]->isBlocked())
                    [ {{ __('settings.auth.priorities.modules.description.blocked') }} ]
                @else
                    [ {{ __('settings.auth.priorities.modules.description', ['module' => $authSettings->priorities[0]->prettyServices()]) }} ]
                @endif
            </div>
        </div>
    @endif
</div>
