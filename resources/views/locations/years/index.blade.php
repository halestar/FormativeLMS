@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="border-bottom d-flex justify-content-between align-items-baseline mb-3 @if(count($errors)) d-none @endif"
             id="add-header">
            <h2>{{ __('system.menu.years') }}</h2>
            <button
                    class="btn btn-primary"
                    type="button"
                    onclick="$('#add-container,#add-header').toggleClass('d-none')"
            ><i class="fa-solid fa-plus border-end pe-2 me-2"></i>{{ __('locations.years.add') }}</button>
        </div>

        <div class="card mb-2 text-bg-light @if(!count($errors)) d-none @endif" id="add-container">
            <form action="{{ route('locations.years.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-floating">
                                <input
                                        type="text"
                                        class="form-control @error('label') is-invalid @enderror"
                                        id="year-label"
                                        name="label"
                                        placeholder="{{ __('locations.years.label') }}"
                                />
                                <label for="year-label">{{ __('locations.years.label') }}</label>
                                <x-utilities.error-display key="label">{{ $errors->first('label') }}</x-utilities.error-display>
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
                                />
                                <label for="year_start">{{ __('locations.years.start') }}</label>
                                <x-utilities.error-display key="year_start">{{ $errors->first('year_start') }}</x-utilities.error-display>
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
                                />
                                <label for="year_end">{{ __('locations.years.end') }}</label>
                                <x-utilities.error-display key="year_end">{{ $errors->first('year_end') }}</x-utilities.error-display>
                            </div>
                        </div>
                        <div class="col-2 align-self-center text-center">
                            <button type="submit" class="btn btn-primary">{{ __('locations.years.add') }}</button>
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


        <div class="card">
            <div class="card-body p-0">
                <div class="accordion accordion-flush mt-2" id="years-container">
                    <div class="accordion-body p-0">
                        <div class="list-group list-group-flush">
                            @if($currentYear)
                                <a
                                        href="{{ route('locations.years.show', ['year' => $currentYear->id]) }}"
                                        class="list-group-item list-group-item-action border-2 bg-info-subtle"
                                >
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="permission-name fw-bolder">{{ $currentYear->label }}</span>
                                        <span class="badge text-bg-primary">{{ __('locations.years.current') }}</span>
                                        <span class="text-muted text-sm">
                                                {{ __('locations.years.duration', ['start' => $currentYear->year_start->format(config('lms.date_format')), 'end' => $currentYear->year_end->format(config('lms.date_format'))]) }}
                                        </span>
                                    </div>
                                    @if($currentYear->terms()->count() > 0)
                                        <div class="d-flex justify-content-start align-items-center mb-2 lh-1">
                                            @foreach($currentYear->campuses as $campus)
                                                {!! $campus->iconHtml('normal', 'align-self-start me-2') !!}
                                                <span class="me-3">{{ $currentYear->campusTerms($campus)->get()->pluck('label')->join(', ') }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </a>
                            @endif
                            @foreach(\App\Models\Locations\Year::whereNot('id', $currentYear->id)->get() as $year)
                                <a
                                        href="{{ route('locations.years.show', ['year' => $year->id]) }}"
                                        class="list-group-item list-group-item-action"
                                >
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="permission-name fw-bolder">{{ $year->label }}</span>
                                        <span class="text-muted text-sm">
                                                {{ __('locations.years.duration', ['start' => $year->year_start->format(config('lms.date_format')), 'end' => $year->year_end->format(config('lms.date_format'))]) }}
                                            </span>
                                    </div>
                                    @if($year->terms()->count() > 0)
                                        <div class="d-flex justify-content-start align-items-center mb-2 lh-1">
                                            @foreach($year->campuses as $campus)
                                                {!! $campus->iconHtml('normal', 'align-self-start me-2') !!}
                                                <span class="me-3">{{ $year->campusTerms($campus)->get()->pluck('label')->join(', ') }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
