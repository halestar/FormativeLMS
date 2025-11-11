@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <h1 class="border-bottom mb-3">{{ __('locations.period.new') }}</h1>
        <form method="POST" action="{{ route('locations.periods.store', $campus) }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-8">
                    <div>
                        <label for="name" class="form-label">{{ __('locations.period.name') }}</label>
                        <input
                                type="text"
                                class="form-control @error('name') is-invalid enderror"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                        />
                        <x-utilities.error-display key="name">{{ $errors->first('name') }}</x-utilities.error-display>
                    </div>
                </div>
                <div class="col-md-4">
                    <div>
                        <label for="abbr" class="form-label">{{ __('locations.period.abbr') }}</label>
                        <input
                                type="text"
                                class="form-control @error('abbr') is-invalid @enderror"
                                id="abbr"
                                name="abbr"
                                value="{{ old('abbr') }}"
                        />
                        <x-utilities.error-display key="abbr">{{ $errors->first('abbr') }}</x-utilities.error-display>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="day" class="form-label">{{ __('locations.period.day') }}</label>
                    <select name="day" id="day" class="form-select @error('day') is-invalid @enderror">
                        @foreach(\App\Classes\Days::weekdaysOptions() as $id => $day)
                            <option value="{{ $id }}" @if(old('day') == $id) selected @endif>{{ $day }}</option>
                        @endforeach
                    </select>
                    <x-utilities.error-display key="day">{{ $errors->first('day') }}</x-utilities.error-display>
                </div>
                <div class="col-md-4">
                    <label for="start" class="form-label">{{ __('locations.period.start') }}</label>
                    <input
                            type="time"
                            class="form-control @error('start') is-invalid @enderror"
                            id="start"
                            name="start"
                            value="{{ old('start') }}"
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
                            value="{{ old('end') }}"
                    />
                    <x-utilities.error-display key="end">{{ $errors->first('end') }}</x-utilities.error-display>
                </div>
            </div>
            <div class="row">
                <button class="btn btn-primary col mx-2"
                        type="submit">{{ trans_choice('locations.period.create', 1) }}</button>
                <a class="btn btn-secondary col mx-2" role="button"
                   href="{{ route('locations.campuses.show', ['campus' => $campus]) }}">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
