@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <h1 class="border-bottom mb-3">{{ __('locations.period.edit') }}</h1>
        <form method="POST" action="{{ route('locations.periods.update', $period) }}">
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <div class="col-md-7">
                    <div>
                        <label for="name" class="form-label">{{ __('locations.period.name') }}</label>
                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @endif"
                            id="name"
                            name="name"
                            value="{{ $period->name }}"
                        />
                        <x-error-display key="name">{{ $errors->first('name') }}</x-error-display>
                    </div>
                </div>
                <div class="col-md-3">
                    <div>
                        <label for="abbr" class="form-label">{{ __('locations.period.abbr') }}</label>
                        <input
                            type="text"
                            class="form-control @error('abbr') is-invalid @endif"
                            id="abbr"
                            name="abbr"
                            value="{{ $period->abbr }}"
                        />
                        <x-error-display key="abbr">{{ $errors->first('abbr') }}</x-error-display>
                    </div>
                </div>
                <div class="col-md-2 align-self-center">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="active" name="active" @if($period->active) checked @endif value="1">
                        <label class="form-check-label" for="active">{{ __('locations.period.active') }}</label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="day" class="form-label">{{ __('locations.period.day') }}</label>
                    <select name="day" id="day" class="form-select @error('day') is-invalid @endif">
                        @foreach(\App\Classes\Days::weekdaysOptions() as $id => $day)
                            <option value="{{ $id }}" @if($period->day == $id) selected @endif>{{ $day }}</option>
                        @endforeach
                    </select>
                    <x-error-display key="day">{{ $errors->first('day') }}</x-error-display>
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
                    <x-error-display key="start">{{ $errors->first('start') }}</x-error-display>
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
                    <x-error-display key="end">{{ $errors->first('end') }}</x-error-display>
                </div>
            </div>
            <div class="row">
                <button class="btn btn-primary col mx-2" type="submit">{{ trans_choice('locations.period.update', 1) }}</button>
                <button
                    class="btn btn-danger col mx-2"
                    type="button"
                    onclick="confirmDelete('{{ __('locations.period.delete.confirm') }}', '{{ route('locations.periods.destroy', $period) }}')"
                >{{ trans_choice('locations.period.delete', 1) }}</button>
                <a class="btn btn-secondary col mx-2" role="button" href="{{ route('locations.campuses.show', ['campus' => $period->campus_id]) }}">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
