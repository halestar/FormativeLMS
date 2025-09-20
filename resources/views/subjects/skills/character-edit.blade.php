@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <form action="{{ route('subjects.skills.update.character', $skill) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <div class="col-6">
                    <label for="designation" class="form-label">{{ __('subjects.skills.designation')  }}</label>
                    <input
                            type="text"
                            class="form-control @error('designation') is-invalid @enderror"
                            id="designation"
                            name="designation"
                            aria-describedby="designationHelp"
                            value="{{ $skill->designation }}"
                            required
                    />
                    <div id="designationHelp" class="form-text">{{ __('subjects.skills.designation.help') }}</div>
                    <x-error-display key="designation">{{ $errors->first('designation') }}</x-error-display>
                </div>
                <div class="col-6">
                    <label for="name" class="form-label">{{ __('subjects.skills.name')  }}</label>
                    <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            value="{{ $skill->name }}"
                            aria-describedby="nameHelp"
                    />
                    <div id="nameHelp" class="form-text">{{ __('subjects.skills.name.help') }}</div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <div class="form-check form-switch">
                        <input
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                id="active"
                                value="1"
                                name="active"
                                @checked($skill->active)
                                @disabled(!$skill->canActivate())
                        />
                        <label class="form-check-label" for="active">{{ __('subjects.subject.active') }}</label>
                    </div>
                    @if(!$skill->canActivate())
                        <div class="alert alert-danger">{{ __('subjects.skills.activate.cant') }}</div>
                    @endif
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">{{ trans_choice('subjects.skills.level',2) }}</label>
                    <br/>
                    @foreach(\App\Models\CRUD\Level::all() as $level)
                        <div class="form-check form-check-inline">
                            <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="level-{{ $level->id  }}"
                                    name="levels[]"
                                    value="{{ $level->id }}"
                                    @checked(in_array($level->id, $skill->levels->pluck('id')->toArray()))
                            />
                            <label class="form-check-label" for="level-{{ $level->id  }}">{{ $level->name }}</label>
                        </div>
                    @endforeach
                    <br/>
                    <div id="levelsHelp" class="form-text">{{ __('subjects.skills.level.help') }}</div>
                    @error('levels')
                    <div class="alert alert-danger">{{ $errors->first('levels') }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label for="description" class="form-label">{{ __('subjects.skills.description')  }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                              name="description" rows="5">{{ $skill->description }}</textarea>
                    <div id="descriptionHelp" class="form-text">{{ __('subjects.skills.description.help') }}</div>
                    <x-error-display key="description">{{ $errors->first('description') }}</x-error-display>
                </div>
            </div>
            <div class="row">
                <button type="submit"
                        class="btn btn-primary col mx-2">{{ __('subjects.skills.knowledge.update') }}</button>
                <a href="{{ route('subjects.skills.show.knowledge', $skill) }}"
                   class="btn btn-secondary col mx-2">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
@push('head_scripts')
    <x-utilities.text-editor instance-name="textarea#description"/>
@endpush
