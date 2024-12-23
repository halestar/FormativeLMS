<div class="card">
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @foreach($periods as $period)
                <li class="list-group-item" wire:key="{{ $period['id'] }}">
                    <div class="row">
                        <div class="col-sm-1 text-center align-self-center">
                            <div class="checkbox-inline">
                                <input
                                    type="checkbox"
                                    id="copy-period-{{ $period['id'] }}"
                                    value="{{ $period['id'] }}"
                                    class="form-check-input"
                                    @if($period['selected']) checked @endif
                                    wire:click="updateSelected({{ $period['id'] }}, {{ !$period['selected']? 'true': 'false' }})"
                                />
                            </div>
                        </div>
                        <div class="col-sm-3 align-self-center">
                            <div class="form-floating">
                                <input
                                    type="text"
                                    class="form-control"
                                    id="copy-period-name-{{ $period['id'] }}"
                                    placeholder="{{ __('locations.period.name') }}"
                                    value="{{ $period['name'] }}"
                                    wire:change="updateName({{ $period['id'] }}, $event.target.value)"
                                />
                                <label for="copy-period-name-{{ $period['id'] }}">{{ __('locations.period.name') }}</label>
                            </div>
                        </div>
                        <div class="col-sm-2 align-self-center">
                            <div class="form-floating">
                                <input
                                    type="text"
                                    class="form-control"
                                    id="copy-period-abbr-{{ $period['id'] }}"
                                    placeholder="{{ __('locations.period.abbr') }}"
                                    value="{{ $period['abbr'] }}"
                                    wire:change="updateAbbr({{ $period['id'] }}, $event.target.value)"
                                />
                                <label for="copy-period-abbr-{{ $period['id'] }}">{{ __('locations.period.abbr') }}</label>
                            </div>
                        </div>
                        <div class="col-sm-2 align-self-start">
                            <div class="form-floating">
                                <select
                                    class="form-select"
                                    id="copy-period-day-{{ $period['id'] }}"
                                    aria-label="{{ __('locations.period.day') }}"
                                    wire:change="updateDay({{ $period['id'] }}, $event.target.value)"
                                >
                                    @foreach(\App\Classes\Days::weekdaysOptions() as $dayId => $dayName)
                                        <option value="{{ $dayId }}" @if($period['day'] === $dayId) selected @endif>{{ $dayName }}</option>
                                    @endforeach
                                </select>
                                <label for="copy-period-day-{{ $period['id'] }}">{{ __('locations.period.day') }}</label>
                            </div>
                        </div>
                        <div class="col-sm-2 align-self-center">
                            <div class="form-floating">
                                <input
                                    type="time"
                                    class="form-control"
                                    id="copy-period-start-{{ $period['id'] }}"
                                    placeholder="{{ __('locations.period.start') }}"
                                    value="{{ $period['start'] }}"
                                />
                                <label for="copy-period-abbr-{{ $period['id'] }}">{{ __('locations.period.start') }}</label>
                            </div>
                        </div>
                        <div class="col-sm-2 align-self-center">
                            <div class="form-floating">
                                <input
                                    type="time"
                                    class="form-control"
                                    id="copy-period-end-{{ $period['id'] }}"
                                    placeholder="{{ __('locations.period.end') }}"
                                    value="{{ $period['end'] }}"
                                />
                                <label for="copy-period-abbr-{{ $period['id'] }}">{{ __('locations.period.end') }}</label>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
