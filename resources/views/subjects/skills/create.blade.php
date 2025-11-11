@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <form action="{{ route('subjects.skills.store') }}" method="POST" x-data="{ skill_type: '{{ $category? 'subject': 'global' }}' }">
            @csrf
            <div class="row mb-3">
                <div class="col-6">
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="skill_type"
                            id="skill_type_subject"
                            value="subject"
                            x-model="skill_type"
                        />
                        <label class="form-check-label" for="skill_type_subject">{{ __('subjects.skills.subject') }}</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="skill_type"
                            id="skill_type_global"
                            value="global"
                            x-model="skill_type"
                        >
                        <label class="form-check-label" for="skill_type_global">{{ trans_choice('subjects.skills.global', 1) }}</label>
                    </div>

                    <div class="mt-2" x-show="skill_type === 'subject'">
                        <livewire:utilities.selectors.subject-matter-selector/>
                        <div id="subjectId" class="form-text">{{ __('subjects.skills.subject.help') }}</div>
                    </div>
                    <div class="mt-2" x-show="skill_type === 'global'">
                        <div id="subjectId" class="form-text">{{ __('subjects.skills.global.help') }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <label for="designation" class="form-label">{{ __('subjects.skills.designation')  }}</label>
                    <input
                            type="text"
                            class="form-control @error('designation') is-invalid @enderror"
                            id="designation"
                            name="designation"
                            aria-describedby="designationHelp"
                            value="{{ old('designation', '') }}"
                            required
                    />
                    <div id="designationHelp" class="form-text">{{ __('subjects.skills.designation.help') }}</div>
                    <x-utilities.error-display key="designation">{{ $errors->first('designation') }}</x-utilities.error-display>
                </div>
            </div>
            <div class="row mb-3">
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
                <div class="col-6" x-show="skill_type === 'subject'">
                    <label for="cat_designation"
                           class="form-label">{{ __('subjects.skills.category.designation') }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="cat_designation" name="cat_designation" />
                        <span class="input-group-text fw-bolder">:</span>
                        <select name="category_id" id="category_id" class="form-select">
                            @foreach($parentCategories as $cat)
                                <option
                                    value="{{ $cat->id }}"
                                    @selected(($category && $category->id == $cat->id) || old('category_id', null) == $cat->id)
                                >{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="catDesignationHelp"
                         class="form-text">{{ __('subjects.skills.category.designation.help') }}</div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">{{ trans_choice('subjects.skills.level',2) }}</label>
                    <br/>
                    @foreach(\App\Models\SystemTables\Level::all() as $level)
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
                    <livewire:utilities.simple-text-editor
                            instance="description"
                            :content="old('description', '')"
                            class="form-control @error('description') is-invalid @enderror"
                            rows="5"
                    />
                    <div id="descriptionHelp" class="form-text">{{ __('subjects.skills.description.help') }}</div>
                    <x-utilities.error-display key="description">{{ $errors->first('description') }}</x-utilities.error-display>
                </div>
            </div>
            <div class="row">
                <button type="submit"
                        class="btn btn-primary col mx-2">{{ __('subjects.skills.add') }}</button>
                <a href="{{ route('subjects.skills.index') }}"
                   class="btn btn-secondary col mx-2">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
