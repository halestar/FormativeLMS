<div>
    <div class="input-group mb-3">
        <span class="input-group-text">{{ __('locations.years.select') }}</span>
        <select class="form-select" wire:model="selectedYearId" wire:change="setYear">
            @foreach($years as $year)
                <option value="{{ $year->id }}">{{ $year->label }}</option>
            @endforeach
        </select>
    </div>
    @if(!$schema)
        <div class="alert alert-warning">
            <p>
                {{ __('learning.grades.translations.none') }}
                <button type="button" class="ms-2 btn btn-primary btn-sm" wire:click="create">{{ __('learning.grades.translations.create') }}</button>
            </p>
            @if($importFrom->count() > 0)
                <div class="input-group mt-2">
                    <span class="input-group-text">{{ __('learning.grades.translations.import') }}</span>
                    <select class="form-select" wire:change="selectedImportFrom">
                        @foreach($importFrom as $import)
                            <option value="{{ $import->id }}">{{ $import->year->label }} ({{ $import->campus->name }})</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-primary btn-sm ms-2" wire:click="import">{{ __('common.import') }}</button>
                </div>
            @endif
        </div>
    @else
        <div class="row mb-3">
            <div class="col-md">
                <h4 class="mb-3 border-bottom">{{ __('learning.grades.translations.opportunities') }}</h4>
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        wire:model="show_opportunity_grade"
                        id="show_opportunities"
                        wire:click="saveProperties"
                    >
                    <label class="form-check-label" for="show_opportunities">
                        {{ __('learning.grades.translations.show') }}
                    </label>
                </div>
                <div class="form-check">
                    <input
                            class="form-check-input"
                            type="checkbox"
                            wire:model="translate_opportunity_grade"
                            id="translate_opportunities"
                            wire:click="saveProperties"
                    >
                    <label class="form-check-label" for="translate_opportunities">
                        {{ __('learning.grades.translations.translate') }}
                    </label>
                </div>
            </div>
            <div class="col-md">
                <h4 class="mb-3 border-bottom">{{ __('learning.grades.translations.criteria') }}</h4>
                <div class="form-check">
                    <input
                            class="form-check-input"
                            type="checkbox"
                            wire:model="show_criteria_grade"
                            id="show_criteria"
                            wire:click="saveProperties"
                    >
                    <label class="form-check-label" for="show_criteria">
                        {{ __('learning.grades.translations.show') }}
                    </label>
                </div>
                <div class="form-check">
                    <input
                            class="form-check-input"
                            type="checkbox"
                            wire:model="translate_criteria_grade"
                            id="translate_criteria"
                            wire:click="saveProperties"
                    >
                    <label class="form-check-label" for="translate_criteria">
                        {{ __('learning.grades.translations.translate') }}
                    </label>
                </div>
            </div>
            <div class="col-md">
                <h4 class="mb-3 border-bottom">{{ __('learning.grades.translations.overall') }}</h4>
                <div class="form-check">
                    <input
                            class="form-check-input"
                            type="checkbox"
                            wire:model="show_overall_grade"
                            id="show_overall"
                            wire:click="saveProperties"
                    >
                    <label class="form-check-label" for="show_overall">
                        {{ __('learning.grades.translations.show') }}
                    </label>
                </div>
                <div class="form-check">
                    <input
                            class="form-check-input"
                            type="checkbox"
                            wire:model="translate_overall_grade"
                            id="translate_overall"
                            wire:click="saveProperties"
                    >
                    <label class="form-check-label" for="translate_overall">
                        {{ __('learning.grades.translations.translate') }}
                    </label>
                </div>
            </div>
        </div>
        <div class="mb-3 d-flex justify-content-between align-items-center border-bottom">
            <h4>{{ __('learning.grades.translations.table') }}</h4>
            <button class="btn btn-primary btn-sm" wire:click="addRow">
                <i class="fa-solid fa-plus"></i>{{ __('learning.grades.translations.table.row.new') }}
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle text-center">
                <thead>
                    <th>{{ __('learning.grades.translations.table.row.min') }}</th>
                    <th>{{ __('learning.grades.translations.table.row.max') }}</th>
                    <th>{{ __('learning.grades.translations.table.row.grade') }}</th>
                    <th>{{ __('learning.grades.translations.table.row.applies.opportunities') }}</th>
                    <th>{{ __('learning.grades.translations.table.row.applies.criteria') }}</th>
                    <th>{{ __('learning.grades.translations.table.row.applies.overall') }}</th>
                    <th>{{ __('learning.grades.translations.table.row.applies.reports') }}</th>
                    <th>{{ __('learning.grades.translations.table.row.applies.transcripts') }}</th>
                    <th></th>
                </thead>
                <tbody>
                    @foreach($grade_translations->rows as $row)
                        <tr x-data="
                            {
                                min: {{ $row->min }},
                                max: {{ $row->max }},
                                grade: '{{ $row->grade }}',
                                appliesToOpportunities: {{ $row->appliesToOpportunities? 'true': 'false' }},
                                appliesToCriteria: {{ $row->appliesToCriteria? 'true': 'false' }},
                                appliesToOverall: {{ $row->appliesToOverall? 'true': 'false' }},
                                appliesToReports: {{ $row->appliesToReports? 'true': 'false' }},
                                appliesToTranscripts: {{ $row->appliesToTranscripts? 'true': 'false' }}
                             }"
                            wire:key="{{ $loop->index }}"
                            wire:replace
                        >
                            <td>
                                <input
                                    x-on:change="$wire.updateRow({{ $loop->index }}, $data)"
                                    type="number"
                                    id="min_{{ $loop->index }}"
                                    class="form-control text-center"
                                    x-model="min"
                                />
                            </td>
                            <td>
                                <input
                                    x-on:change="$wire.updateRow({{ $loop->index }}, $data)"
                                    type="number"
                                    id="max_{{ $loop->index }}"
                                    class="form-control text-center"
                                    x-model="max"
                                />
                            </td>
                            <td>
                                <input
                                    x-on:change="$wire.updateRow({{ $loop->index }}, $data)"
                                    type="text"
                                    id="grade_{{ $loop->index }}"
                                    class="form-control text-center"
                                    x-model="grade"
                                />
                            </td>
                            <td>
                                <input
                                    x-on:change="$wire.updateRow({{ $loop->index }}, $data)"
                                    type="checkbox"
                                    id="opportunities_{{ $loop->index }}"
                                    class="form-check-input"
                                    x-model="appliesToOpportunities"
                                />
                            </td>
                            <td>
                                <input
                                    x-on:change="$wire.updateRow({{ $loop->index }}, $data)"
                                    type="checkbox"
                                    id="criteria_{{ $loop->index }}"
                                    class="form-check-input"
                                    x-model="appliesToCriteria"
                                />
                            </td>
                            <td>
                                <input
                                    x-on:change="$wire.updateRow({{ $loop->index }}, $data)"
                                    type="checkbox"
                                    id="overall_{{ $loop->index }}"
                                    class="form-check-input"
                                    x-model="appliesToOverall"
                                />
                            </td>
                            <td>
                                <input
                                    wire:click="updateRow({{ $loop->index }}, $data)"
                                    type="checkbox"
                                    id="reports_{{ $loop->index }}"
                                    class="form-check-input"
                                    x-model="appliesToReports"
                                />
                            </td>
                            <td>
                                <input
                                    x-on:change="$wire.updateRow({{ $loop->index }}, $data)"
                                    type="checkbox"
                                    id="transcripts_{{ $loop->index }}"
                                    class="form-check-input"
                                    x-model="appliesToTranscripts"
                                />
                            </td>
                            <td>
                                <button
                                    class="btn btn-sm btn-danger"
                                    wire:click="removeRow({{ $loop->index }})"
                                ><i class="fa-solid fa-times"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
