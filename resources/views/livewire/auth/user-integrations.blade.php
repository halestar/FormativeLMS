<div>
    <ul class="list-group">
        @forelse($integrators as $integrator)
            <li class="list-group-item text-bg-secondary" wire:key="{{ $integrator['integrator']->id }}">
                <div class="integrator-container d-flex justify-content-between align-items-center">
                    <span>{{ $integrator['integrator']->name }}</span>
                </div>
            </li>
            @foreach($integrator['services'] as $service)
                <li class="list-group-item text-bg-secondary d-flex justify-content-between align-items-center ps-5"
                    wire:key="{{ $integrator['integrator']->id . "_" . $service['service']->id }}">
                    <span>{{ $service['service']->name }}</span>
                    @if($service['connection'])
                        @if($service['service']->canConfigure($person))
                            <a class="btn btn-success btn-sm text-lowercase text-sm"
                               role="button"
                               href="{{ $service['service']->configurationUrl($person) }}"
                            >{{ __('common.configure') }}</a>
                        @else
                            <livewire:utilities.model-switch
                                    :model="$service['connection']"
                                    key="enabled"
                                    el-id="service_{{ $service['service']->id }}"
                            />
                        @endif
                    @else
                        <a x-show="hovering"
                           x-cloak
                           class="btn btn-success btn-sm text-lowercase text-sm"
                           role="button"
                           href="{{ $service['service']->registrationUrl($person) }}"
                        >{{ __('common.register') }}</a>
                    @endif
                </li>
            @endforeach
        @empty
            <li class="list-group-item list-group-item-info text-muted">{{ __('integrators.integrations.available.no') }}</li>
        @endforelse
    </ul>
</div>
