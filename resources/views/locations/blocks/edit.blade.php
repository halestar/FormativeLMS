@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <h1 class="border-bottom mb-3">{{ __('locations.block.edit') }}</h1>
        <form method="POST" action="{{ route('locations.blocks.update', $block) }}">
            @csrf
            @method('PUT')
            <div class="row justify-content-center">
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('locations.block.name') }}</label>
                        <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                value="{{ $block->name }}"
                        />
                        <x-error-display key="name">{{ $errors->first('name') }}</x-error-display>
                    </div>
                </div>
                <div class="col-md-2 align-self-center">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="active" name="active"
                               @if($block->active) checked @endif value="1">
                        <label class="form-check-label" for="active">{{ __('locations.block.active') }}</label>
                    </div>
                </div>
            </div>
            <div class="d-flex mb-3 justify-content-center">
                @foreach(\App\Classes\Days::weekdaysOptions() as $dayId => $dayName)
                    <div class="mx-2">
                        <label for="day-select-{{ $dayId }}" class="form-label">{{ $dayName }}</label>
                        <select
                                class="form-select @error('periods') is-invalid @endif"
                                id="day-select-{{ $dayId }}"
                                name="periods[]"
                                multiple
                                size="{{ $block->campus->periods($dayId)->active()->count() }}"
                        >
                            @foreach($block->campus->periods($dayId)->active()->get() as $period)
                                <option
                                        value="{{ $period->id }}"
                                        @if($block->periods()->where('id', $period->id)->exists()) selected @endif
                                >{{ $period->abbr }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>
            @error('periods')
            <div class="alert alert-danger">{{ $errors->first('periods') }}</div>
            @enderror
            <div class="row">
                <button class="btn btn-primary col mx-2"
                        type="submit">{{ trans_choice('locations.block.update', 1) }}</button>
                <button
                        class="btn btn-danger col mx-2"
                        type="button"
                        onclick="confirmDelete('{{ __('locations.block.delete.confirm') }}', '{{ route('locations.blocks.destroy', $block) }}')"
                >{{ __('locations.block.delete') }}</button>
                <a class="btn btn-secondary col mx-2" role="button"
                   href="{{ route('locations.campuses.show', ['campus' => $block->campus_id]) }}">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
