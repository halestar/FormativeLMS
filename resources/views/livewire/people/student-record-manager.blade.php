<div class="w-100">
    @if($editing)
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-black bg-opacity-50">
            <div class="position-relative top-50 start-50 w-50 h-50 translate-middle">
                <div class="card">
                    <h5 class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ __('people.student.record.manage') }}</span>
                        <button
                                type="button"
                                class="btn btn-danger btn-sm rounded rounded-pill"
                                x-on:click="$wire.set('editing', false)"
                                aria-label="{{ trans('common.close') }}"
                        >Editing Student Records
                        </button>
                    </h5>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                <tr>
                                    <th>{{ trans_choice('locations.years', 1) }}</th>
                                    <th>{{ trans_choice('crud.level', 1) }}</th>
                                    <th>{{ trans_choice('locations.campus', 1) }}</th>
                                    <th>{{ __('common.date.start') }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($studentRecords as $record)
                                    <tr wire:key="{{ $record->id }}"
                                        @if(!$record->end_date) class="border-bottom" @endif>
                                        <td>
                                            <select class="form-select"
                                                    wire:change="updateYear({{ $record->id }}, $event.target.value)">
                                                @foreach(\App\Models\Locations\Year::all() as $year)
                                                    <option value="{{ $year->id }}"
                                                            @if($year->id == $record->year_id) selected @endif>{{ $year->label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select"
                                                    wire:change="updateLevel({{ $record->id }}, $event.target.value)">
                                                @foreach(\App\Models\SystemTables\Level::all() as $level)
                                                    <option value="{{ $level->id }}"
                                                            @if($level->id == $record->level_id) selected @endif>{{ $level->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select"
                                                    wire:change="updateCampus({{ $record->id }}, $event.target.value)">
                                                @foreach($record->level->campuses as $campus)
                                                    <option value="{{ $campus->id }}"
                                                            @if($campus->id == $record->campus_id) selected @endif>{{ $campus->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input
                                                    type="date"
                                                    class="form-control @error('startDate-' . $record->id) is-invalid @enderror"
                                                    wire:change="updateStartDate({{ $record->id }}, $event.target.value)"
                                                    value="{{ $record->start_date->format('Y-m-d') }}"
                                            />
                                            <x-error-display
                                                    key="startDate-{{ $record->id }}">{{ $errors->first('startDate-' . $record->id) }}</x-error-display>
                                        </td>
                                        <td>
                                            @if($record->end_date)
                                                <span class="badge text-bg-danger">{{ __('people.student.withdrawn') }}</span>
                                            @else
                                                <button type="button" class="btn btn-warning btn-sm"
                                                        wire:click="withdrawStudent({{ $record->id }})">{{ __('people.student.withdraw') }}</button>
                                            @endif
                                        </td>
                                        <td>
                                            <button
                                                    type="button"
                                                    class="btn btn-danger btn-sm rounded rounded-pill"
                                                    wire:click="deleteRecord({{ $record->id }})"
                                            >
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @if($record->end_date)
                                        <tr class="border-bottom">
                                            <td>
                                                <div class="form-floating">
                                                    <select
                                                            class="form-select"
                                                            id="withdraw-reason-{{ $record->id }}"
                                                            aria-label="Withdraw Reason"
                                                            wire:change="updateDismissalReason({{ $record->id }}, $event.target.value)"
                                                    >
                                                        <option selected>{{ __('people.student.withdraw.reason.select') }}</option>
                                                        @foreach(\App\Models\SystemTables\DismissalReason::all() as $reason)
                                                            <option value="{{ $reason->id }}"
                                                                    @if($reason->id == $record->dismissal_reason_id) selected @endif>{{ $reason->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label for="withdraw-reason-{{ $record->id }}">{{ __('people.student.withdraw.reason') }}</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating">
                                                    <input
                                                            type="date"
                                                            class="form-control @error('endDate-' . $record->id) is-invalid @enderror"
                                                            id="end_date-{{ $record->id }}"
                                                            placeholder="{{ date('Y-m-d') }}"
                                                            value="{{ $record->end_date->format('Y-m-d') }}"
                                                            wire:change="updateEndDate({{ $record->id }}, $event.target.value)"
                                                            aria-label="Withdraw Date"
                                                    >
                                                    <label for="floatingInput">{{ __('people.student.withdraw.date') }}</label>
                                                    <x-error-display
                                                            key="endDate-{{ $record->id }}">{{ $errors->first('endDate-' . $record->id) }}</x-error-display>
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <div class="form-floating">
                                                    <textarea
                                                            class="form-control"
                                                            placeholder="Withdraw Notes"
                                                            id="dismissal_note_{{ $record->id }}"
                                                            wire:change="updateDismissalNote({{ $record->id }}, $event.target.value)"
                                                    >{!! $record->dismissal_note !!}</textarea>
                                                    <label for="dismissal_note_{{ $record->id }}">{{ __('people.student.withdraw.notes') }}</label>
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <button
                                                        type="button"
                                                        class="btn btn-primary"
                                                        wire:click="undoWithdrawal({{ $record->id }})"
                                                >{{ __('people.student.withdraw.undo') }}</button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary"
                                    wire:click="addRecord()">{{ trans_choice('people.student.record.add',1) }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <h6 class="d-flex justify-content-between align-items-baseline">
            <div>
                <strong class="me-2">{{ trans_choice('crud.level',1) }}:</strong>
                @if($person->student())
                    {{ $person->student()->level->name }}
                    ({{ $person->student()->campus->name }})
                @else
                    <span class="badge text-bg-warning">{{ __('common.enrolled.no') }}</span>
                @endif
            </div>
            @can('people.edit')
                <button
                        type="button"
                        x-on:click="$wire.set('editing', true)"
                        class="btn btn-primary btn-sm rounded rounded-pill"
                >{{ trans_choice('people.student.record.edit',2) }}</button>
            @endcan
        </h6>
    @endif
</div>
