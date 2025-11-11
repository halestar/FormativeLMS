@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <h1 class="border-bottom mb-3">{{ __('subjects.subject.edit') }}</h1>
        <form method="POST" action="{{ route('subjects.subjects.update', $subject) }}">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('subjects.subject.name') }}</label>
                        <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                value="{{ $subject->name }}"
                        />
                        <x-utilities.error-display key="name">{{ $errors->first('name') }}</x-utilities.error-display>
                    </div>
                </div>
                <div class="col-md-4 align-self-center">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="active" name="active"
                               @if($subject->active) checked @endif>
                        <label class="form-check-label" for="active">{{ __('subjects.subject.active') }}</label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label" for="color">{{ __('subjects.subject.color') }}</label>
                    <input type="color" class="form-control" id="color" name="color" value="{{ $subject->color }}"/>
                </div>
                <div class="col-md-6 align-self-end">
                    <div class="input-group">
                        <label for="required_terms"
                               class="input-group-text">{{ __('subjects.subject.required_terms') }}</label>
                        <input type="number" class="form-control" id="required_terms" name="required_terms"
                               value="{{ $subject->required_terms?? 0 }}"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <button class="btn btn-primary col mx-2"
                        type="submit">{{ trans_choice('subjects.subject.update', 1) }}</button>
                <a class="btn btn-secondary col mx-2" role="button"
                   href="{{ route('subjects.subjects.index') }}">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
