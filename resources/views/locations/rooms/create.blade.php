@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="border-bottom display-4 text-primary mb-5">{{ __('locations.rooms.new') }}</div>
        <form action="{{ route('locations.rooms.store') }}" method="POST">
            @csrf
            @if(!$building)
                <div class="alert alert-primary">{{ trans_choice('locations.rooms.free', 1) }}</div>
            @else
                <div class="input-group mb-3">
                    <label class="input-group-text"
                           for="area">{{ __('locations.rooms.location', ['building' => $building->name]) }}</label>
                    <select name="area_id" id="area" class="form-select @error('area_id') is-invalid @enderror"
                            required>
                        @foreach($building->buildingAreas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                        @endforeach
                    </select>
                    <x-error-display key="area_id">{{ $errors->first('area_id') }}</x-error-display>
                </div>
            @endif
            <div class="row mb-3">
                <div class="col-md-8">
                    <label for="name" class="form-label">{{ __('locations.rooms.name') }}</label>
                    <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="{{ __('locations.rooms.name') }}"
                            value="{{ old('name') }}"
                            required
                    />
                    <x-error-display key="name">{{ $errors->first('name') }}</x-error-display>
                </div>
                <div class="col-md-4">
                    <label for="capacity" class="form-label">{{ __('locations.rooms.capacity') }}</label>
                    <input
                            type="number"
                            name="capacity"
                            id="capacity"
                            class="form-control @error('capacity') is-invalid @enderror"
                            placeholder="{{ __('locations.rooms.capacity') }}"
                            value="{{ old('capacity') }}"
                            required
                    />
                    <x-error-display key="capacity">{{ $errors->first('capacity') }}</x-error-display>
                </div>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">{{ __('locations.rooms.notes') }}</label>
                <textarea
                        name="notes"
                        id="notes"
                        class="form-control"
                        placeholder="{{ __('locations.rooms.notes') }}"
                        rows="3"
                >{{ old('notes') }}</textarea>
            </div>
            <div class="row">
                <button type="submit" class="btn btn-primary col-12">{{ __('locations.rooms.add') }}</button>
            </div>
        </form>
    </div>
@endsection
