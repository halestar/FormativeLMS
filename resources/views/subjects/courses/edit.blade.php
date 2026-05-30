@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container pb-5">
        <form method="POST" action="{{ route('subjects.courses.update', $course) }}">
            @csrf
            @method('PUT')

            <div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-4">
                <h1 class="h2 mb-0 text-primary">
                    <i class="fas fa-edit me-2"></i>{{ __('subjects.course.edit') }}
                </h1>
                <a href="{{ route('subjects.courses.index', ['subject' => $course->subject_id]) }}"
                   class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('common.cancel') }}
                </a>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm border-primary border-top border-3 h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="m-0 fw-bold text-primary">{{ __('subjects.course.edit') }}</h5>
                        </div>
                        <div class="card-body">

                            <div class="row mb-3">
                                <div class="col-md-12 mb-3">
                                    <label for="name"
                                           class="form-label fw-bold text-dark">{{ __('subjects.course.name') }}</label>
                                    <input type="text"
                                           class="form-control form-control-lg shadow-sm @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ $course->name }}"/>
                                    <x-utilities.error-display
                                            key="name">{{ $errors->first('name') }}</x-utilities.error-display>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="subtitle"
                                           class="form-label fw-bold text-dark">{{ __('subjects.course.subtitle') }}</label>
                                    <input type="text" class="form-control shadow-sm" id="subtitle" name="subtitle"
                                           value="{{ $course->subtitle }}"/>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="code"
                                           class="form-label fw-bold text-dark">{{ __('subjects.course.code') }}</label>
                                    <input type="text" class="form-control shadow-sm" id="code" name="code"
                                           value="{{ $course->code }}"/>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="description"
                                       class="form-label fw-bold text-dark">{{ __('subjects.course.description') }}</label>
                                <textarea class="form-control shadow-sm" id="description" name="description"
                                          rows="3">{{ $course->description }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label for="credits"
                                       class="form-label fw-bold text-dark">{{ __('subjects.course.credits') }}</label>
                                <input type="number" step="any" name="credits" class="form-control shadow-sm w-50"
                                       id="credits" value="{{ $course->credits }}"/>
                            </div>

                            <hr class="text-muted mb-4">

                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" name="on_transcript" id="on_transcript"
                                               class="form-check-input" value="1"
                                               @if($course->on_transcript) checked @endif />
                                        <label class="form-check-label fw-bold text-dark"
                                               for="on_transcript">{{ __('subjects.course.on_transcript') }}</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" name="gb_required" id="gb_required"
                                               class="form-check-input" value="1"
                                               @if($course->gb_required) checked @endif />
                                        <label class="form-check-label fw-bold text-dark"
                                               for="gb_required">{{ __('subjects.course.gb_required') }}</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" name="honors" id="honors" class="form-check-input"
                                               value="1" @if($course->honors) checked @endif />
                                        <label class="form-check-label fw-bold text-dark"
                                               for="honors">{{ __('subjects.course.honors') }}</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" name="ap" id="ap" class="form-check-input" value="1"
                                               @if($course->ap) checked @endif />
                                        <label class="form-check-label fw-bold text-dark"
                                               for="ap">{{ __('subjects.course.ap') }}</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" name="can_assign_honors" id="can_assign_honors"
                                               class="form-check-input" value="1"
                                               @if($course->can_assign_honors) checked @endif />
                                        <label class="form-check-label fw-bold text-dark"
                                               for="can_assign_honors">{{ __('subjects.course.can_assign_honors') }}</label>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="form-check form-switch fs-5">
                                        <input class="form-check-input" type="checkbox" role="switch" id="active"
                                               name="active" value="1" @if($course->active) checked @endif>
                                        <label class="form-check-label ms-2 pt-1 fw-bold text-dark"
                                               for="active">{{ __('subjects.subject.active') }}</label>
                                    </div>
                                </div>
                            </div>

                            <hr class="text-muted mb-4">

                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-lg shadow-sm" type="submit">
                                    <i class="fas fa-save me-2"></i>{{ trans_choice('subjects.course.update', 1) }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <livewire:assessment.skill-selector :course="$course"
                                                        :selected-skill-ids="$course->skills->pluck('id')->toArray()"/>
                </div>

                <div id="skill-ids-container">
                    @foreach($course->skills as $skill)
                        <input type="hidden" name="skills[]" value="{{ $skill->id }}" id="skill_{{ $skill->id }}"/>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('skill-selector.skills-added', (event) => {
                const newElement = document.createElement('input');
                newElement.type = 'hidden';
                newElement.name = 'skills[]';
                newElement.id = 'skill_' + event.skill;
                newElement.value = event.skill;
                document.getElementById('skill-ids-container').appendChild(newElement);
            });
            Livewire.on('skill-selector.skills-removed', (event) => {
                const elementToRemove = document.getElementById('skill_' + event.skill);
                if (elementToRemove) {
                    elementToRemove.remove();
                }
            });
        });
    </script>
@endpush