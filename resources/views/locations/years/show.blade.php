@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="border-bottom d-flex justify-content-between align-items-baseline mb-3 @if(count($errors)) d-none @endif"
             id="add-header">
            <h2>{{ __('locations.terms.years', ['years' => $year->label]) }}</h2>

            <div>
                <button
                        class="btn btn-primary"
                        onclick="$('#add-container,#add-header').toggleClass('d-none')"
                        type="button"
                >
                    <i class="fa fa-plus pe-1 me-1 border-end"></i>
                    {{ __('locations.terms.add') }}
                </button>
                <button
                        class="btn btn-secondary"
                        onclick="$('#edit-container,#add-header').toggleClass('d-none')"
                        type="button"
                >
                    <i class="fa fa-edit pe-1 me-1 border-end"></i>
                    {{ __('locations.years.edit') }}
                </button>
                @if($year->canDelete())
                    <button
                            class="btn btn-danger"
                            type="button"
                            onclick="confirmDelete('{{ __('locations.years.delete.confirm') }}', '{{ route('locations.years.destroy', ['year' => $year->id]) }}')"
                    >
                        <i class="fa fa-times pe-1 me-1 border-end"></i>
                        {{ __('locations.years.delete') }}
                    </button>
                @endif
            </div>
        </div>
        <div class="card mb-2 text-bg-light @if(!$errors->hasAny(['campus_id','term_label','term_start','term_end'])) d-none @endif"
             id="add-container">
            <form action="{{ route('locations.years.terms.store', ['year' => $year->id]) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="form-floating">
                                <select
                                        class="form-select @error('campus_id') is-invalid @enderror"
                                        id="campus_id"
                                        name="campus_id"
                                        aria-label="{{ trans_choice('locations.campus', 1) }}"
                                >
                                    @foreach(\App\Models\Locations\Campus::all() as $campus)
                                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                    @endforeach
                                </select>
                                <label for="campus_id">{{ trans_choice('locations.campus', 1) }}</label>
                                <x-error-display key="campus_id">{{ $errors->first('campus_id') }}</x-error-display>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-floating">
                                <input
                                        type="text"
                                        class="form-control @error('term_label') is-invalid @enderror"
                                        id="term-label"
                                        name="term_label"
                                        placeholder="{{ __('locations.terms.label') }}"
                                />
                                <label for="term-label">{{ __('locations.terms.label') }}</label>
                                <x-error-display key="term_label">{{ $errors->first('term_label') }}</x-error-display>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-floating">
                                <input
                                        type="date"
                                        class="form-control @error('term_start') is-invalid @enderror"
                                        id="term_start"
                                        name="term_start"
                                        min="{{ $year->year_start->format('Y-m-d') }}"
                                        max="{{ $year->year_end->format('Y-m-d') }}"
                                        placeholder="{{ __('locations.terms.start') }}"
                                        value="{{ $year->year_start->format('Y-m-d') }}"
                                />
                                <label for="term_start">{{ __('locations.terms.start') }}</label>
                                <x-error-display key="term_start">{{ $errors->first('term_start') }}</x-error-display>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-floating">
                                <input
                                        type="date"
                                        class="form-control @error('term_end') is-invalid @enderror"
                                        id="term_end"
                                        name="term_end"
                                        min="{{ $year->year_start->format('Y-m-d') }}"
                                        max="{{ $year->year_end->format('Y-m-d') }}"
                                        placeholder="{{ __('locations.terms.end') }}"
                                        value="{{ $year->year_end->format('Y-m-d') }}"
                                />
                                <label for="term_end">{{ __('locations.terms.end') }}</label>
                                <x-error-display key="term_end">{{ $errors->first('term_end') }}</x-error-display>
                            </div>
                        </div>
                        <div class="col-2 align-self-center text-center">
                            <button type="submit" class="btn btn-primary">{{ __('locations.terms.add') }}</button>
                            <button
                                    type="button"
                                    class="btn btn-secondary"
                                    onclick="$('#add-container,#add-header').toggleClass('d-none')"
                            >{{ __('common.cancel') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card mb-2 text-bg-light @if(!$errors->hasAny(['label','year_start','year_end'])) d-none @endif"
             id="edit-container">
            <form action="{{ route('locations.years.update', ['year' => $year->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-floating">
                                <input
                                        type="text"
                                        class="form-control @error('label') is-invalid @enderror"
                                        id="year-label"
                                        name="label"
                                        value="{{ $year->label }}"
                                        placeholder="{{ __('locations.years.label') }}"
                                />
                                <label for="year-label">{{ __('locations.years.label') }}</label>
                                <x-error-display key="label">{{ $errors->first('label') }}</x-error-display>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-floating">
                                <input
                                        type="date"
                                        class="form-control @error('year_start') is-invalid @enderror"
                                        id="year_start"
                                        name="year_start"
                                        placeholder="{{ __('locations.years.start') }}"
                                        value="{{ $year->year_start->format('Y-m-d') }}"
                                />
                                <label for="year_start">{{ __('locations.years.start') }}</label>
                                <x-error-display key="year_start">{{ $errors->first('year_start') }}</x-error-display>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-floating">
                                <input
                                        type="date"
                                        class="form-control @error('year_end') is-invalid @enderror"
                                        id="year_end"
                                        name="year_end"
                                        placeholder="{{ __('locations.years.end') }}"
                                        value="{{ $year->year_end->format('Y-m-d') }}"
                                />
                                <label for="year_end">{{ __('locations.years.end') }}</label>
                                <x-error-display key="year_end">{{ $errors->first('year_end') }}</x-error-display>
                            </div>
                        </div>
                        <div class="col-2 align-self-center text-center">
                            <button type="submit" class="btn btn-primary">{{ __('locations.years.update') }}</button>
                            <button
                                    type="button"
                                    class="btn btn-secondary"
                                    onclick="$('#edit-container,#add-header').toggleClass('d-none')"
                            >{{ __('common.cancel') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div id="terms-container" class="row">
            @foreach($campuses as $campus)
                <div class="col-md-6 mb-3">
                    <livewire:locations.term-editor :campus="$campus" :year="$year"/>
                </div>
            @endforeach
        </div>
    </div>
@endsection
