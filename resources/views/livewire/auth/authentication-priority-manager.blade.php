<div>
    @if($editing)
    <div class="border-bottom d-flex justify-content-between align-items-center mb-3">
        <h3>{{ __('settings.auth.priorities') }}</h3>
        <button
                type="button"
                class="btn btn-danger"
                x-on:click="$wire.set('editing', false)"
        >{{ __('common.cancel') }}</button>
    </div>
    <ul class="list-group">
        <li class="list-group-item">
            <div class="row align-items-center">
                <div class="col-12">
                    <h3 class="row align-content-start border-bottom mb-2">
                        {{ __('settings.auth.priorities.default') }}: {{ __('settings.auth.priorities.default.help') }}
                    </h3>
                    <div class="row">
                        <div class="col">
                            <div class="input-group mb-3">
                                <span class="input-group-text">{{ __('settings.auth.priorities.modules.use') }}</span>
                                <select
                                        class="form-select"
                                        wire:change="updateAuthentication(0, $event.target.value, $('input[name=choose_auth_0]:checked').map((i, e) => $(e).val()).get())"
                                >
                                    <option value="">{{ __('settings.auth.priorities.modules.select') }}</option>
                                    @foreach(\App\Classes\Auth\Authenticator::all() as $module => $moduleClass)
                                        <option
                                                value="{{ $module }}"
                                                @selected(!is_array($priorities[0]->authModules) && $priorities[0]->authModules == $module)
                                        >{{ ($moduleClass)::driverPrettyName() }}</option>
                                    @endforeach
                                    <option
                                            value="choose"
                                            @if(is_array($priorities[0]->authModules))
                                                selected
                                            @endif
                                    >{{ __('settings.auth.priorities.modules.choose') }}</option>
                                </select>
                            </div>
                            @if(is_array($priorities[0]->authModules))
                                <div class="alert alert-info p-3">
                                    <div class="row row-cols-3">
                                        @foreach(\App\Classes\Auth\Authenticator::all() as $module => $moduleClass)
                                            <div class="form-check col">
                                                <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        name="choose_auth_0"
                                                        id="choose_auth_0_{{ $module }}"
                                                        value="{{ $module }}"
                                                        @checked(in_array($module, $priorities[0]->authModules))
                                                        wire:click="updateAuthentication(0, 'choose', $('input[name=choose_auth_0]:checked').map((i, e) => $(e).val()).get())"
                                                >
                                                <label class="form-check-label"
                                                       for="choose_auth_0_{{ $module }}">
                                                    {{ ($moduleClass)::driverPrettyName() }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    <div class="my-3 border-bottom d-flex justify-content-between align-items-center">
        <h2>{{ __('settings.auth.priorities.additional') }}</h2>

        <button type="button" class="btn btn-primary" wire:click="addPriority()">
            <span class="border-end pe-2 me-2"><i class="fa fa-plus"></i></span>{{ __('settings.auth.priority') }}
        </button>
    </div>
    <ul wire:sortable="reorderPriorities" class="list-group">
    @foreach($priorities as $priority)
        @continue($priority->priority == \App\Classes\Auth\AuthenticationDesignation::DEFAULT_PRIORITY)
        <li wire:sortable.item="{{ $priority->priority }}" wire:key="{{ $priority->priority }}" class="list-group-item">
            <div class="row align-items-center">
                <div class="col-1">
                    <span wire:sortable.handle class="show-as-grab me-2 display-1"><i
                                class="fa-solid fa-grip-lines-vertical"></i></span>
                </div>
                <div class="col-10">
                    <div class="row align-content-start border-bottom mb-2">
                        {{ __('settings.auth.priorities.number') .  $priority->priority }}
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="input-group mb-3">
                                <span class="input-group-text">{{ __('settings.role.assign') }}</span>
                                <select
                                        class="form-select"
                                        wire:change="addRoleToPriority({{ $priority->priority }}, $event.target.value)"
                                        x-on:change="$event.target.selectedIndex = 0"
                                >
                                    <option value="">{{ __('people.roles.select') }}</option>
                                    @foreach(\App\Models\Utilities\SchoolRoles::whereNotIn('id', array_merge(...array_map(fn($v) => array_keys($v->roles), $priorities)))->excludeAdmin()->get() as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="alert alert-info p-3">
                                @foreach($priority->roles as $roleId => $role)
                                    <span class="badge text-bg-primary mx-2 p-2 show-as-action align-items-center">
                                        {{ $role }}
                                        <span
                                                class="mx-1 border-start ps-2"
                                                wire:click="removeRoleFromPriority({{$priority->priority}}, {{ $roleId }})"
                                        ><i class="text-danger fs-6 fa fa-times"></i></span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <span class="input-group-text">{{ __('settings.auth.priorities.modules.use') }}</span>
                                <select
                                        class="form-select"
                                        wire:change="updateAuthentication({{ $priority->priority }}, $event.target.value, $('input[name=choose_auth_{{ $priority->priority }}]:checked').map((i, e) => $(e).val()).get())"
                                >
                                    <option value="">{{ __('settings.auth.priorities.modules.select') }}</option>
                                    @foreach(\App\Classes\Auth\Authenticator::all() as $module => $moduleClass)
                                        <option
                                                value="{{ $module }}"
                                                @selected(!is_array($priority->authModules) && $priority->authModules == $module)
                                        >{{ ($moduleClass)::driverPrettyName() }}</option>
                                    @endforeach
                                    <option
                                            value="choose"
                                            @selected(is_array($priority->authModules))
                                    >{{ __('settings.auth.priorities.modules.choose') }}</option>
                                </select>
                            </div>
                            @if(is_array($priority->authModules))
                            <div class="alert alert-info p-3">
                                <div class="row row-cols-3">
                                    @foreach(\App\Classes\Auth\Authenticator::all() as $module => $moduleClass)
                                        <div class="form-check col">
                                            <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    name="choose_auth_{{ $priority->priority }}"
                                                    id="choose_auth_{{ $priority->priority }}_{{ $module }}"
                                                    value="{{ $module }}"
                                                    @checked(in_array($module, $priority->authModules))
                                                    wire:click="updateAuthentication({{ $priority->priority }}, 'choose', $('input[name=choose_auth_{{ $priority->priority }}]:checked').map((i, e) => $(e).val()).get())"
                                            >
                                            <label class="form-check-label"
                                                   for="choose_auth_{{ $priority->priority }}_{{ $module }}">
                                                {{ ($moduleClass)::driverPrettyName() }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-1">
                    <span class="show-as-action me-2 display-1 text-danger" wire:click="removePriority({{ $priority->priority }})"><i
                                class="fa-solid fa-times"></i></span>
                </div>
            </div>
        </li>
    @endforeach
    </ul>
    @if($changed)
        <div class="alert alert-danger">
            <h3 class="alert-heading">{{ __('settings.auth.priorities.warning.heading') }}</h3>
            <p>
                {!! __('settings.auth.priorities.warning') !!}
            </p>
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-danger mx-2" wire:click="applyChanges">{{ __('common.apply.changes') }}</button>
                <button type="button" class="btn btn-secondary mx-2" wire:click="revertChanges">{{ __('common.revert.changes') }}</button>
            </div>
        </div>
    @endif

    @else
        <div class="border-bottom d-flex justify-content-between align-items-center mb-3">
            <h3>{{ __('settings.auth.priorities') }}</h3>
            <button
                type="button"
                class="btn btn-secondary"
                x-on:click="$wire.set('editing', true)"
            >{{ __('common.edit') }}</button>
        </div>
        @foreach($priorities as $priority)
            @continue($priority->priority == \App\Classes\Auth\AuthenticationDesignation::DEFAULT_PRIORITY)
            <div class="alert alert-info">
                <h5>
                    {{ __('settings.auth.priorities.number') .  $priority->priority }}:
                    {{ __('settings.auth.priorities.description', ['roles' => implode(', ', $priority->roles)]) }}
                </h5>
                <h6>
                    @if(is_array($priority->authModules))
                        [ {{ __('settings.auth.priorities.modules.description.choose', ['modules' => $priority->prettyModules()]) }} ]
                    @else
                        [ {{ __('settings.auth.priorities.modules.description', ['module' => $priority->prettyModules()]) }} ]
                    @endif
                </h6>
            </div>
        @endforeach
        <div class="alert alert-primary">
            <h5>
                {{ __('settings.auth.priorities.default') }}: {{ __('settings.auth.priorities.default.help') }}
            </h5>
            <h6>
                @if(is_array($priority->authModules))
                    [ {{ __('settings.auth.priorities.modules.description.choose', ['modules' => $priority->prettyModules()]) }} ]
                @else
                    [ {{ __('settings.auth.priorities.modules.description', ['module' => $priority->prettyModules()]) }} ]
                @endif
            </h6>
        </div>
    @endif
</div>
