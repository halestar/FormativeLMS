<div>
    <ul class="list-group">
        @forelse($integrators as $integratorId => $integrator)
            <li class="list-group-item text-bg-secondary" wire:key="{{ $integratorId }}">
                <div class="integrator-container d-flex justify-content-between align-items-center">
                    <span>{{ $integrator['name'] }}</span>
                </div>
            </li>
            @foreach($integrator['services'] as $serviceId => $service)
                <li class="list-group-item text-bg-secondary d-flex justify-content-between align-items-center ps-5"
                    wire:key="{{ $integratorId . "_" . $serviceId }}">
                    <span>{{ $service['name'] }}</span>
                    @if($service['connection'])
                        @if($service['configuration_url'])
                            <a class="btn btn-success btn-sm text-lowercase text-sm"
                               role="button"
                               href="{{ $service['configuration_url'] }}"
                            >{{ __('common.configure') }}</a>
                        @else
                            <livewire:utilities.model-switch
                                    :model="$service['connection']"
                                    property="enabled"
                                    el-id="service_{{ $serviceId }}"
                            />
                        @endif
                    @else
                        <a class="btn btn-success btn-sm text-lowercase text-sm"
                           role="button"
                           href="{{ $service['registration_url'] }}"
                        >{{ __('common.register') }}</a>
                    @endif
                </li>
            @endforeach
        @empty
            <li class="list-group-item list-group-item-info text-muted">{{ __('integrators.integrations.available.no') }}</li>
        @endforelse
    </ul>
</div>
