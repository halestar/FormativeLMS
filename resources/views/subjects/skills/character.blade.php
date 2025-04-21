@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <form action="{{ route('subjects.skills.store.character') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-6">
                    <label for="designation" class="form-label">{{ __('subjects.skills.designation')  }}</label>
                    <input
                        type="text"
                        class="form-control @error('designation') is-invalid @endif"
                        id="designation"
                        name="designation"
                        aria-describedby="designationHelp"
                        value="{{ old('designation', '') }}"
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
                        value="{{ old('name', '') }}"
                        aria-describedby="nameHelp"
                    />
                    <div id="nameHelp" class="form-text">{{ __('subjects.skills.name.help') }}</div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <input type="hidden" name="category_id" value="{{ $category->id }}" />
                    <label for="cat_designation_id" class="form-label">{{ __('subjects.skills.category.designation') }}</label>
                    <div class="input-group">
                        <select class="form-select" id="cat_designation_id" name="cat_designation_id">
                            @foreach (\App\Models\CRUD\SkillCategoryDesignation::all() as $designation)
                                <option value="{{ $designation->id }}" @selected(old('cat_designation_id', '') == $designation->id)>{{ $designation->name }}</option>
                            @endforeach
                        </select>
                        <span class="input-group-text">: {{ $category->name }}</span>
                    </div>
                    <div id="catDesignationHelp" class="form-text">{{ __('subjects.skills.category.designation.help') }}</div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">{{ trans_choice('subjects.skills.level',2) }}</label>
                    <br />
                    @foreach(\App\Models\CRUD\Level::all() as $level)
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="level-{{ $level->id  }}"
                                name="levels[]"
                                value="{{ $level->id }}"
                                @checked(in_array($level->id, old('levels', [])))
                            />
                            <label class="form-check-label" for="level-{{ $level->id  }}">{{ $level->name }}</label>
                        </div>
                    @endforeach
                    <br />
                    <div id="levelsHelp" class="form-text">{{ __('subjects.skills.level.help') }}</div>
                    @error('levels')
                    <div class="alert alert-danger">{{ $errors->first('levels') }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label for="description" class="form-label">{{ __('subjects.skills.description')  }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', '') }}</textarea>
                    <div id="descriptionHelp" class="form-text">{{ __('subjects.skills.description.help') }}</div>
                    <x-error-display key="description">{{ $errors->first('description') }}</x-error-display>
                </div>
            </div>
            <div class="row">
                <button type="submit" class="btn btn-primary col mx-2">{{ __('subjects.skills.knowledge.add') }}</button>
                <a href="{{ route('subjects.skills.index') }}" class="btn btn-secondary col mx-2">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
@push('head_scripts')
    <x-utilities.text-editor instance-name="textarea#description" />
@endpush
