<div class="w-100">
    @if($editing)
        <div class="card">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span>{{ __('people.assign_roles_to_users') }}</span>
                <button
                    type="button"
                    class="btn btn-danger btn-sm rounded rounded-pill"
                    x-on:click="$wire.set('editing', false)"
                    aria-label="{{ trans('common.close') }}"
                >{{ __('people.editing_roles') }}</button>
            </h5>
            <div class="card-body">
                <div class="row border-bottom mb-3">
                    <div class="col-4">{{ __('settings.base_roles') }}</div>
                    <div class="col-8">{{ __('settings.additional_roles') }}</div>
                </div>
                <div class="row">
                    <div class="col-3 border-end">
                        @foreach($baseRoles as $baseRole)
                            <div class="form-check form-switch ms-3 mb-2" wire:key="{{ $baseRole['id'] }}">
                                <label class="form-check-label" for="base_role_{{ $baseRole['id'] }}">{{ $baseRole['name'] }}</label>
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    role="switch"
                                    wire:click="changeBaseRole({{ $baseRole['id'] }}, {{ !$baseRole['hasRole']? "true": "false" }})"
                                    id="base_role_{{ $baseRole['id'] }}"
                                    @if($baseRole['hasRole']) checked @endif
                                />
                            </div>
                        @endforeach
                    </div>
                    <div class="col-9">
                        <div class="row row-cols-auto">
                            @foreach(\App\Models\Utilities\SchoolRoles::normalRoles()->get() as $normalRole)
                                <div
                                    class="col m-1 p-1 border rounded @if($person->hasRole($normalRole)) text-bg-success @else text-bg-secondary @endif"
                                    wire:key="{{ $normalRole->id }}"
                                >
                                    <div class="form-check form-switch">
                                        <label class="form-check-label" for="base_role_{{ $normalRole->id }}">{{ $normalRole->name }}</label>
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            wire:click="changeNormalRole({{ $normalRole->id }}, {{ !$person->hasRole($normalRole)? 'true': 'false' }})"
                                            id="base_role_{{ $normalRole->id }}"
                                            @if($person->hasRole($normalRole)) checked @endif
                                        />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <h6 class="d-flex justify-content-between align-items-baseline">
            <div>
                <strong class="me-2">{{ __('settings.roles') }}:</strong> {{ $person->roles->pluck('name')->join(', ') }}
            </div>
            @can('people.assign.roles')
                <button
                    type="button"
                    x-on:click="$wire.set('editing', true)"
                    class="btn btn-primary btn-sm rounded rounded-pill"
                >{{ __('settings.edit_roles') }}</button>
            @endcan
        </h6>
    @endif
</div>
