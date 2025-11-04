<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="list-group">
                @foreach($schoolClasses as $schoolClass)
                    <button
                        type="button"
                        class="list-group-item list-group-item-action @if($schoolClass->id == $classSelected) active @endif"
                        style="background-color: {{ $schoolClass->subject->color }}; color: {{ $schoolClass->subject->getTextHex() }}"
                        wire:click="selectClass({{ $schoolClass->id }})"
                        wire:key="{{ $schoolClass->id }}"
                    >
                        @if($schoolClass->id == $classSelected)<i class="fa-solid fa-caret-right me-3"></i>@endif
                        {{ $sessions[$schoolClass->id]->first()->nameWithSchedule }}
                    </button>
                @endforeach
            </div>
        </div>
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                <h4>{{ __('system.menu.criteria') }}</h4>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" wire:click="addCriteria">
                        {{ __('learning.criteria.add') }}
                    </button>
                    @if($classCriteria->count() > 0)
                    <button type="button" class="btn btn-warning btn-sm" @click="$('#copy-criteria-dialog').removeClass('d-none')">
                        {{ __('learning.criteria.copy.short') }}
                    </button>
                    @else
                        <button type="button" class="btn btn-warning btn-sm" @click="$('#import-criteria-dialog').removeClass('d-none')">
                            {{ __('learning.criteria.import.short') }}
                        </button>
                    @endif
                </div>
            </div>
            @if($classCriteria->count() > 0)
            <div class="alert alert-info d-none" id="copy-criteria-dialog" x-data="{ checked: true }">
                <div class="alert-heading d-flex justify-content-between align-items-center">
                    <h5>{{ __('learning.criteria.copy') }}</h5>
                    <button type="button" class="btn btn-primary" @click="checked = !checked">
                        {{ __('common.toggle.all') }}
                    </button>
                </div>
                <div class="d-flex flex-wrap mb-3">
                    @foreach($schoolClasses as $schoolClass)
                        @continue($schoolClass->id == $classSelected)
                        @continue($schoolClass->hasCriteria())
                        <div class="form-check m-2">
                            <input
                                type="checkbox"
                                id="copy_class_{{ $schoolClass->id }}"
                                name="copy_class[]"
                                value="{{ $schoolClass->id }}"
                                x-bind:checked="checked"
                            >
                            <label for="copy_class_{{ $schoolClass->id }}">{{ $sessions[$schoolClass->id]->first()->nameWithSchedule }}</label>
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    <button
                        type="button"
                        class="btn btn-primary col mx-2"
                        wire:click="copyToClasses($('input[name=\'copy_class[]\']:checked').map(function () { return $(this).val() }).toArray())"
                    >{{ __('common.copy') }}</button>
                    <button
                            type="button"
                            class="btn btn-secondary col mx-2"
                            @click="$('#copy-criteria-dialog').addClass('d-none')"
                    >{{ __('common.cancel') }}</button>
                </div>
            </div>
            @else
                <div class="alert alert-info d-none" id="import-criteria-dialog">
                    <div class="input-group mb-3">
                        <span class="input-group-text">{{ __('learning.criteria.import') }}</span>
                        <select class="form-select" wire:model.live="importYearId">
                            @foreach($importYears as $year)
                                <option value="{{ $year->id }}">{{ $year }}</option>
                            @endforeach
                        </select>
                        @if($importYearId)
                        <select class="form-select" wire:model="importClassId" id="import-class">
                            @foreach($importClasses[$importYearId] as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                    <div class="row">
                        <button
                                type="button"
                                class="btn btn-primary col mx-2"
                                wire:click="importCriteria($('#import-class').val())"
                        >{{ __('common.import') }}</button>
                        <button
                                type="button"
                                class="btn btn-secondary col mx-2"
                                @click="$('#import-criteria-dialog').addClass('d-none')"
                        >{{ __('common.cancel') }}</button>
                    </div>
                </div>
            @endif
            @foreach($sessions[$classSelected] as $session)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">{{ $session->term->label }} {{ trans_choice('locations.terms', 1) }}</h4>
                        <button class="btn btn-primary btn-sm" wire:click="copyCriteria({{ $session->id  }})">{{ __('learning.criteria.weight.copy') }}</button>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <thead>
                                <th></th>
                                <th>{{ __('learning.criteria.name') }}</th>
                                <th>{{ __('learning.criteria.abbr') }}</th>
                                <th>{{ __('learning.criteria.weight') }}</th>
                                <th></th>
                            </thead>
                            <tbody>
                            @foreach($classCriteria as $criteria)
                                <tr wire:key="{{ $criteria->id }}">
                                    <td>
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="connected_{{ $criteria->id }}"
                                            value="{{ $criteria->id }}"
                                            @if($session->hasCriteria($criteria))
                                                wire:click="removeSessionCriteria({{ $session->id }}, {{ $criteria->id }})"
                                            @else
                                                wire:click="attachSessionCriteria({{ $session->id }}, {{ $criteria->id }})"
                                            @endif
                                            @checked($session->hasCriteria($criteria))
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            class="form-control"
                                            wire:change="updateCriteriaName({{ $criteria->id }}, $event.target.value)"
                                            id="name_{{ $criteria->id }}"
                                            value="{{ $criteria->name }}"
                                            @disabled(!$session->hasCriteria($criteria))
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="abbr_{{ $criteria->id }}"
                                            value="{{ $criteria->abbreviation }}"
                                            wire:change="updateCriteriaAbbr({{ $criteria->id }}, $event.target.value)"
                                            @disabled(!$session->hasCriteria($criteria))
                                        />
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input
                                                type="number"
                                                wire:change="updateCriteriaWeight({{ $session->id }}, {{ $criteria->id }}, $event.target.value)"
                                                class="form-control"
                                                id="weight_{{ $criteria->id }}"
                                                value="{{ $session->getCriteria($criteria)?->sessionCriteria->weight ?? '' }}"
                                                name="weight"
                                                @disabled(!$session->hasCriteria($criteria))
                                                wire:key="{{ $session->getCriteria($criteria)?->sessionCriteria->weight ?? '' }}"
                                            />
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm"
                                            wire:click="deleteCriteria({{ $criteria->id }})"
                                            @disabled(!$session->hasCriteria($criteria))
                                        >
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            @if(($session->classCriteria()?->count() ?? 0) > 0)
                                <tfoot class="border-top">
                                <tr>
                                    <td colspan="3"></td>
                                    <td class="d-flex justify-content-between align-items-center">
                                        <span class="fs-6 fw-bold">Total:</span>
                                        {{ $session->classCriteria->reduce(fn(?int $carry, \App\Models\SubjectMatter\Learning\ClassCriteria $c) => $carry + $c->sessionCriteria->weight) }}% </td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
