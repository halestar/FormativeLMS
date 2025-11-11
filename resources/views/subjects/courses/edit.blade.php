@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <h1 class="border-bottom mb-3">{{ __('subjects.course.edit') }}</h1>
        <form method="POST" action="{{ route('subjects.courses.update', $course) }}">
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('subjects.course.name') }}</label>
                        <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                value="{{ $course->name }}"
                        />
                        <x-utilities.error-display key="name">{{ $errors->first('name') }}</x-utilities.error-display>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="subtitle" class="form-label">{{ __('subjects.course.subtitle') }}</label>
                        <input
                                type="text"
                                class="form-control"
                                id="subtitle"
                                name="subtitle"
                                value="{{ $course->subtitle }}"
                        />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="code" class="form-label">{{ __('subjects.course.code') }}</label>
                        <input
                                type="text"
                                class="form-control"
                                id="code"
                                name="code"
                                value="{{ $course->code }}"
                        />
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('subjects.course.description') }}</label>
                        <textarea
                                class="form-control"
                                id="description"
                                name="description"
                                rows="3"
                        >{{ $course->description }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row row-cols-4 mb-5">
                <div class="col align-self-center">
                    <div class="input-group">
                        <label for="credits" class="input-group-text">{{ __('subjects.course.credits') }}</label>
                        <input
                                type="text"
                                name="credits"
                                class="form-control"
                                id="credits"
                                value="{{ $course->credits }}"
                        />
                    </div>
                </div>
                <div class="col align-self-start">
                    <div class="form-check mb-2">
                        <input
                                type="checkbox"
                                name="on_transcript"
                                id="on_transcript"
                                class="form-check-input"
                                value="1"
                                @if($course->on_transcript) checked @endif
                        />
                        <label class="form-check-label"
                               for="on_transcript">{{ __('subjects.course.on_transcript') }}</label>
                    </div>
                    <div class="form-check">
                        <input
                                type="checkbox"
                                name="gb_required"
                                id="gb_required"
                                class="form-check-input"
                                value="1"
                                @if($course->gb_required) checked @endif
                        />
                        <label class="form-check-label"
                               for="gb_required">{{ __('subjects.course.gb_required') }}</label>
                    </div>
                </div>
                <div class="col align-self-start">
                    <div class="form-check mb-2">
                        <input
                                type="checkbox"
                                name="honors"
                                id="honors"
                                class="form-check-input"
                                value="1"
                                @if($course->honors) checked @endif
                        />
                        <label class="form-check-label" for="honors">{{ __('subjects.course.honors') }}</label>
                    </div>
                    <div class="form-check">
                        <input
                                type="checkbox"
                                name="ap"
                                id="ap"
                                class="form-check-input"
                                value="1"
                                @if($course->ap) checked @endif
                        />
                        <label class="form-check-label" for="ap">{{ __('subjects.course.ap') }}</label>
                    </div>
                </div>
                <div class="col align-self-start">
                    <div class="form-check mb-2">
                        <input
                                type="checkbox"
                                name="can_assign_honors"
                                id="can_assign_honors"
                                class="form-check-input"
                                value="1"
                                @if($course->can_assign_honors) checked @endif
                        />
                        <label class="form-check-label"
                               for="can_assign_honors">{{ __('subjects.course.can_assign_honors') }}</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="active" name="active"
                               @if($course->active) checked @endif value="1">
                        <label class="form-check-label" for="active">{{ __('subjects.subject.active') }}</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <button class="btn btn-primary col mx-2"
                        type="submit">{{ trans_choice('subjects.course.update', 1) }}</button>
                <a class="btn btn-secondary col mx-2" role="button"
                   href="{{ route('subjects.courses.index', ['subject' => $course->subject_id]) }}">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
