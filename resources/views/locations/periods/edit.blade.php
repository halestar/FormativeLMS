@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <form method="POST" action="{{ route('locations.periods.update', $period) }}">
            @csrf
            @method('PUT')
            <div class="border-bottom mb-3  d-flex justify-content-between align-items-center">
                <h3>{{ __('locations.period.edit') }}</h3>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="active" name="active"
                           @checked($period->active) value="1">
                    <label class="form-check-label" for="active">{{ __('locations.period.active') }}</label>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-8">
                    <div>
                        <label for="name" class="form-label">{{ __('locations.period.name') }}</label>
                        <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                value="{{ $period->name }}"
                        />
                        <x-utilities.error-display key="name">{{ $errors->first('name') }}</x-utilities.error-display>
                    </div>
                </div>
                <div class="col-md-4">
                    <div>
                        <label for="abbr" class="form-label">{{ __('locations.period.abbr') }}</label>
                        <input
                                type="text"
                                class="form-control @error('abbr') is-invalid @endif"
                                id="abbr"
                                name="abbr"
                                value="{{ $period->abbr }}"
                        />
                        <x-utilities.error-display key="abbr">{{ $errors->first('abbr') }}</x-utilities.error-display>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="day" class="form-label">{{ __('locations.period.day') }}</label>
                    <select name="day" id="day" class="form-select @error('day') is-invalid @endif">
                        @foreach(\App\Classes\Settings\Days::weekdaysOptions() as $id => $day)
                            <option value="{{ $id }}" @if($period->day == $id) selected @endif>{{ $day }}</option>
                        @endforeach
                    </select>
                    <x-utilities.error-display key="day">{{ $errors->first('day') }}</x-utilities.error-display>
                </div>
                <div class="col-md-4">
                    <label for="start" class="form-label">{{ __('locations.period.start') }}</label>
                    <input
                            type="time"
                            class="form-control @error('start') is-invalid @endif"
                            id="start"
                            name="start"
                            value="{{ $period->start->format('H:i') }}"
                    />
                    <x-utilities.error-display key="start">{{ $errors->first('start') }}</x-utilities.error-display>
                </div>
                <div class="col-md-4">
                    <label for="start" class="form-label">{{ __('locations.period.end') }}</label>
                    <input
                            type="time"
                            class="form-control @error('end') is-invalid @endif"
                            id="end"
                            name="end"
                            value="{{ $period->end->format('H:i') }}"
                    />
                    <x-utilities.error-display key="end">{{ $errors->first('end') }}</x-utilities.error-display>
                </div>
            </div>
            <div class="row">
                <button class="btn btn-primary col mx-2"
                        type="submit">{{ trans_choice('locations.period.update', 1) }}</button>
                @if($period->canDelete())
                <button
                        class="btn btn-danger col mx-2"
                        type="button"
                        onclick="confirmDelete('{{ __('locations.period.delete.confirm') }}', '{{ route('locations.periods.destroy', $period) }}')"
                >{{ trans_choice('locations.period.delete', 1) }}</button>
                @endif
                <a class="btn btn-secondary col mx-2" role="button"
                   href="{{ route('locations.campuses.edit', ['campus' => $period->campus_id]) }}">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
