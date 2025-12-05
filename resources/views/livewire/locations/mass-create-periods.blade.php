<div>
    <ul class="nav nav-tabs" irole="tablist">
        <li class="nav-item" role="presentation">
            <button
                    class="nav-link @if($tab == 'copy_campus') active @endif"
                    id="home-tab"
                    type="button"
                    role="tab"
                    wire:click="set('tab', 'copy_campus')"
            >{{ __('locations.period.mass.copy.campus.another') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button
                    class="nav-link @if($tab == 'copy_days') active @endif"
                    id="home-tab"
                    type="button"
                    role="tab"
                    wire:click="set('tab', 'copy_days')"
            >{{ __('locations.period.mass.copy.day.another') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button
                    class="nav-link @if($tab == 'mass_create') active @endif"
                    id="profile-tab"
                    type="button"
                    role="tab"
                    wire:click="set('tab', 'mass_create')"
            >{{ __('locations.period.mass.create') }}</button>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active my-3" role="tabpanel">
            @if($tab == 'copy_campus')
                <div class="input-group mb-3">
                    <label for="copy_days" class="input-group-text">{{ __('locations.period.copy') }}</label>
                    <select name="copy_days" id="copy_days" class="form-select" wire:model="copy_day"
                            wire:change="updateCopyPeriods()">
                        <option value="0">{{ __('locations.period.day.all') }}</option>
                        @foreach(\App\Classes\Settings\Days::weekdaysOptions() as $dayId => $dayName)
                            <option value="{{ $dayId }}">{{ $dayName }}</option>
                        @endforeach
                    </select>
                    <label for="copy_campus_id" class="input-group-text">{{ __('common.to') }}</label>
                    <select name="copy_campus_id" id="copy_campus_id" class="form-select" wire:model="copy_campus_id">
                        @foreach($availableCampuses as $availableCampus)
                            <option value="{{ $availableCampus->id }}">{{ $availableCampus->name }}</option>
                        @endforeach
                    </select>
                </div>
                <livewire:locations.mass-create-period-creator wire:model="copyPeriods" :periods="$copyPeriods"/>
                <div class="row mt-3">
                    <button
                            type="button"
                            class="btn btn-primary"
                            wire:confirm="{{ __('locations.period.mass.copy.campus.confirm') }}"
                            wire:click="doCopy()"
                    >{{ __('locations.period.mass.copy.campus.other') }}</button>
                </div>
            @elseif($tab == 'copy_days')
                <div class="input-group mb-3">
                    <label for="copy_from_day"
                           class="input-group-text">{{ __('locations.periods.mass.copy.day.from') }}</label>
                    <select id="copy_from_day" class="form-select" wire:model="copy_from_day"
                            wire:change="updateCopyToDay()">
                        @foreach(\App\Classes\Settings\Days::weekdaysOptions() as $dayId => $dayName)
                            <option value="{{ $dayId }}">{{ $dayName }}</option>
                        @endforeach
                    </select>
                    <label for="copy_to_day" class="input-group-text">{{ __('common.to') }}</label>
                    <select id="copy_to_day" class="form-select" wire:model="copy_to_day"
                            wire:change="updateCopyToDay()">
                        <option value="0">{{ __('locations.period.day.all') }}</option>
                        @foreach(\App\Classes\Settings\Days::weekdaysOptions() as $dayId => $dayName)
                            <option value="{{ $dayId }}">{{ $dayName }}</option>
                        @endforeach
                    </select>
                </div>
                <livewire:locations.mass-create-period-creator wire:model="copyToDay" :periods="$copyToDay"/>
                <div class="row mt-3">
                    <button
                            type="button"
                            class="btn btn-primary"
                            wire:confirm="{{ __('locations.period.mass.copy.day.confirm') }}"
                            wire:click="doCopyToDay()"
                    >{{ __('locations.period.mass.copy.day.other') }}</button>
                </div>
            @elseif($tab == 'mass_create')
                <div class="input-group mb-3">
                    <label for="copy_days" class="input-group-text">{{ __('locations.period.mass.create.on') }} </label>
                    <select name="copy_days" id="copy_days" class="form-select" wire:model="create_day"
                            wire:change="updateMassCreate()">
                        @foreach(\App\Classes\Settings\Days::weekdaysOptions() as $dayId => $dayName)
                            <option value="{{ $dayId }}">{{ $dayName }}</option>
                        @endforeach
                    </select>
                    <label for="create_start"
                           class="input-group-text">{{ __('locations.period.mass.create.starting') }}</label>
                    <input
                            type="time"
                            wire:model="create_start"
                            name="create_start"
                            id="create_start"
                            class="form-control"
                            wire:change="updateMassCreate()"
                    />
                    <label for="create_end"
                           class="input-group-text">{{ __('locations.period.mass.create.ending') }}</label>
                    <input
                            type="time"
                            name="create_end"
                            wire:model="create_end"
                            id="create_end"
                            class="form-control"
                            wire:change="updateMassCreate()"
                    />
                    <label for="create_duration"
                           class="input-group-text">{{ __('locations.period.mass.create.duration') }}</label>
                    <input
                            type="number"
                            name="create_duration"
                            wire:model="create_duration"
                            id="create_duration"
                            class="form-control"
                            wire:change="updateMassCreate()"
                    />
                    <label for="create_between"
                           class="input-group-text">{{ __('locations.period.mass.create.between') }}</label>
                    <input
                            type="number"
                            name="create_between"
                            wire:model="create_between"
                            id="create_between"
                            class="form-control"
                            wire:change="updateMassCreate()"
                    />
                    <label for="create_between" class="input-group-text">{{ trans_choice('common.minute', 2) }}</label>
                </div>

                <livewire:locations.mass-create-period-creator wire:model="createPeriods" :periods="$createPeriods"/>
                <div class="row mt-3">
                    <button
                            type="button"
                            class="btn btn-primary"
                            wire:confirm="{{ __('locations.period.mass.create.confirm') }}"
                            wire:click="massCreate()"
                    >{{ __('locations.period.mass.create') }}</button>
                </div>
            @endif
        </div>
    </div>
</div>
