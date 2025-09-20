<div>
    <ul class="list-group">
        @foreach($integrators as $integrator)
            <li class="list-group-item text-bg-secondary" wire:key="{{ $integrator->id }}">
                <div class="integrator-container d-flex justify-content-between align-items-center">
                    <span>{{ $integrator->name }}</span>
                    @if($integrator->isIntegrated($person))
                        <div x-data="{ hovering: false }"
                             @mouseover="hovering = true"
                             @mouseout="hovering = false"
                        >
                            <span x-show="!hovering" x-cloak class="text-success"><i
                                        class="fa-solid fa-circle"></i></span>
                            <button x-show="hovering"
                                    type="button"
                                    class="btn btn-danger btn-sm"
                                    role="button"
                                    x-cloak
                                    wire:click="removeIntegration({{ $integrator->id }})"
                            >{{ __('common.disconnect') }}</button>
                        </div>
                    @else
                        <div x-data="{ hovering: false }"
                             @mouseover="hovering = true"
                             @mouseout="hovering = false"
                        >
                            <span x-show="!hovering" x-cloak class="text-danger"><i
                                        class="fa-solid fa-circle"></i></span>
                            <a x-show="hovering"
                               class="btn btn-success btn-sm"
                               role="button"
                               x-cloak
                               href="{{ $integrator->integrationUrl($person) }}"
                            >{{ __('common.connect') }}</a>
                        </div>
                    @endif
                </div>
            </li>
            @if($integrator->isIntegrated($person))
                @foreach($integrator->services()->personal()->get() as $service)
                    @continue(!$service->canConnect($person) && !$service->canRegister())
                    <li class="list-group-item text-bg-secondary d-flex justify-content-between align-items-center ps-5"
                        wire:key="{{ $integrator->id . "_" . $service->id }}">
                        <span>{{ $service->name }}</span>
                        @if($service->connect($person))
                            <div x-data="{ hovering: false }"
                                 @mouseover="hovering = true"
                                 @mouseout="hovering = false"
                            >
                                <span x-show="!hovering" x-cloak class="text-success"><i class="fa-solid fa-circle"></i></span>
                                <button x-show="hovering"
                                        x-cloak
                                        type="button"
                                        class="btn btn-danger btn-sm"
                                        role="button"
                                        wire:click="disableService({{ $service->id }})"
                                >{{ __('common.disconnect') }}</button>
                            </div>
                        @elseif($service->canRegister())
                            <div x-data="{ hovering: false }"
                                 @mouseover="hovering = true"
                                 @mouseout="hovering = false"
                            >
                                <span x-show="!hovering" x-cloak class="text-danger"><i class="fa-solid fa-circle"></i></span>
                                <a x-show="hovering"
                                   x-cloak
                                   class="btn btn-success btn-sm text-lowercase text-sm"
                                   role="button"
                                   href="{{ $service->registrationUrl() }}"
                                >{{ __('common.register') }}</a>
                            </div>
                        @else
                            <div x-data="{ hovering: false }"
                                 @mouseover="hovering = true"
                                 @mouseout="hovering = false"
                            >
                                <span x-show="!hovering" x-cloak class="text-danger"><i class="fa-solid fa-circle"></i></span>
                                <button x-show="hovering"
                                        x-cloak
                                        type="button"
                                        class="btn btn-success btn-sm"
                                        role="button"
                                        wire:click="enableService({{ $service->id }})"
                                >{{ __('common.connect') }}</button>
                            </div>
                        @endif
                    </li>
                @endforeach
            @endif
        @endforeach
    </ul>
</div>
