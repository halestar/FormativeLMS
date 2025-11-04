<div>
    @if(!$schema)
        <div class="alert alert-warning">
            <p>
                {{ __('learning.grades.translations.none') }}
            </p>
        </div>
    @else
        <div class="row mb-3">
            <div class="col-md">
                <h4 class="mb-3 border-bottom">{{ __('learning.grades.translations.opportunities') }}</h4>
                @if($schema->show_opportunity_grade)
                    <div class="mb-2 fw-bold">{{ __('learning.grades.translations.show') }}</div>
                @else
                    <div class="mb-2 fw-bold">{{ __('learning.grades.translations.show.no') }}</div>
                @endif
                @if($schema->translate_opportunity_grade)
                    <div class="mb-2 fw-bold">{{ __('learning.grades.translations.translate') }}</div>
                @else
                    <div class="mb-2 fw-bold">{{ __('learning.grades.translations.translate.no') }}</div>
                @endif
            </div>
            <div class="col-md">
                <h4 class="mb-3 border-bottom">{{ __('learning.grades.translations.criteria') }}</h4>
                @if($schema->show_criteria_grade)
                    <div class="mb-2 fw-bold">{{ __('learning.grades.translations.show') }}</div>
                @else
                    <div class="mb-2 fw-bold">{{ __('learning.grades.translations.show.no') }}</div>
                @endif
                @if($schema->translate_criteria_grade)
                    <div class="mb-2 fw-bold">{{ __('learning.grades.translations.translate') }}</div>
                @else
                    <div class="mb-2 fw-bold">{{ __('learning.grades.translations.translate.no') }}</div>
                @endif
            </div>
            <div class="col-md">
                <h4 class="mb-3 border-bottom">{{ __('learning.grades.translations.overall') }}</h4>
                @if($schema->show_overall_grade)
                    <div class="mb-2 fw-bold">{{ __('learning.grades.translations.show') }}</div>
                @else
                    <div class="mb-2 fw-bold">{{ __('learning.grades.translations.show.no') }}</div>
                @endif
                @if($schema->translate_overall_grade)
                    <div class="mb-2 fw-bold">{{ __('learning.grades.translations.translate') }}</div>
                @else
                    <div class="mb-2 fw-bold">{{ __('learning.grades.translations.translate.no') }}</div>
                @endif
            </div>
        </div>
        <div class="mb-3 d-flex justify-content-between align-items-center border-bottom">
            <h4>{{ __('learning.grades.translations.table') }}</h4>
        </div>
        @if(!$schema->grade_translations)
            <div class="alert alert-warning">
                {{ __('learning.grades.translations.table.no') }}
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-sm align-middle text-center table-hover">
                    <thead>
                    <th>{{ __('learning.grades.translations.table.row.min') }}</th>
                    <th>{{ __('learning.grades.translations.table.row.max') }}</th>
                    <th>{{ __('learning.grades.translations.table.row.grade') }}</th>
                    <th>{{ __('learning.grades.translations.table.row.applies.opportunities') }}</th>
                    <th>{{ __('learning.grades.translations.table.row.applies.criteria') }}</th>
                    <th>{{ __('learning.grades.translations.table.row.applies.overall') }}</th>
                    <th>{{ __('learning.grades.translations.table.row.applies.reports') }}</th>
                    <th>{{ __('learning.grades.translations.table.row.applies.transcripts') }}</th>
                    </thead>
                    <tbody>
                    @foreach($schema->grade_translations->rows as $row)
                        <tr>
                            <td>
                                {{ $row->min }}
                            </td>
                            <td>
                                {{ $row->max }}
                            </td>
                            <td>
                                {{ $row->grade }}
                            </td>
                            <td>
                                {{ $row->appliesToOpportunities? __('common.yes'): __('common.no') }}
                            </td>
                            <td>
                                {{ $row->appliesToCriteria? __('common.yes'): __('common.no') }}
                            </td>
                            <td>
                                {{ $row->appliesToOverall? __('common.yes'): __('common.no') }}
                            </td>
                            <td>
                                {{ $row->appliesToReports? __('common.yes'): __('common.no') }}
                            </td>
                            <td>
                                {{ $row->appliesToTranscripts? __('common.yes'): __('common.no') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
</div>
