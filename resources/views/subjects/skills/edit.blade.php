@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <form action="{{ route('subjects.skills.update', $skill) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <div class="col-lg-8">
                    @if($skill->isGlobal())
                        <div class="alert alert-success">
                            <h4 class="alert-heading">{{ trans_choice('subjects.skills.global', 1) }}</h4>
                            {{ __('subjects.skills.global.help') }}
                        </div>
                    @else
                    <div class="d-flex justify-content-start align-items-center flex-wrap mb-3">
                        @foreach($skill->subjects as $subject)
                            <div class="badge fs-5 mx-1" style="background-color: {{ $subject->color }}; text: {{ $subject->getTextHex() }};">
                                <span>
                                    {{ $subject->name }}
                                    ( {{ $subject->campus->abbr }} )
                                </span>
                                <button
                                    type="button"
                                    class="badge text-bg-danger"
                                    onclick="confirmDelete('{{ __('subjects.skills.subject.unlink.confirm') }}', '{{ route('subjects.skills.unlink.subject', ['skill' => $skill->id, 'subject' => $subject->id]) }}')"
                                ><i class="fa-solid fa-times"></i></button>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-start align-items-center">
                        <livewire:utilities.selectors.subject-matter-selector :openTo="$skill->subject"/>
                        <button
                            type="button"
                            class="btn btn-primary mx-2 text-nowrap"
                            onclick="window.location='/academics/skills/{{ $skill->id }}/subject/'+$('#subject_id').val();"
                        >{{ __('subjects.skills.subject.link') }}</button>
                    </div>
                    @endif
                </div>
                <div class="col-lg-4 d-flex justify-content-center align-items-center border rounded text-bg-light">
                    <div class="form-check form-switch fs-3 my-auto">
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
                    <x-utilities.error-display key="designation">{{ $errors->first('designation') }}</x-utilities.error-display>

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
                    <livewire:utilities.simple-text-editor
                            instance="description"
                            :content="$skill->description"
                            class="form-control @error('description') is-invalid @enderror"
                            rows="5"
                    />
                    <div id="descriptionHelp" class="form-text">{{ __('subjects.skills.description.help') }}</div>
                    <x-utilities.error-display key="description">{{ $errors->first('description') }}</x-utilities.error-display>
                </div>
            </div>
            <div class="row">
                <button type="submit"
                        class="btn btn-primary col mx-2">{{ __('subjects.skills.update') }}</button>
                <a href="{{ route('subjects.skills.show', $skill) }}"
                   class="btn btn-secondary col mx-2">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
