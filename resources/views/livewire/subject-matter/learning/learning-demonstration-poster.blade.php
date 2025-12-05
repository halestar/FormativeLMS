<div class="container">
    <div class="border-bottom mb-3 d-flex justify-content-between align-items-center">
        <h2>
            {{ $ld->name }}
        </h2>
        <livewire:utilities.model-switch :model="$ld" property="shareable" :label="__('learning.demonstrations.shareable')" />
    </div>
    <div class="row mb-3">
        <div class="col-md-8">
            <label for="name" class="form-label">{{ __('learning.demonstrations.name') }}</label>
            <input
                    type="text"
                    id="name"
                    wire:model="name"
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="{{ __('learning.demonstrations.name') }}"
                    aria-describedby="name_help"
            />
            <x-utilities.error-display key="name">{{ $errors->first('name') }}</x-utilities.error-display>
            <div id="name_help"
                 class="form-text">{!! __('learning.demonstrations.name.description') !!}</div>
        </div>
        <div class="col-md-4">
            <label for="abbr" class="form-label">{{ __('learning.demonstrations.abbr') }}</label>
            <input
                    type="text"
                    id="abbr"
                    wire:model="abbr"
                    class="form-control @error('abbr') is-invalid @enderror"
                    placeholder="{{ __('learning.demonstrations.abbr') }}"
                    aria-describedby="abbr_help"
            />
            <x-utilities.error-display key="abbr">{{ $errors->first('abbr') }}</x-utilities.error-display>
            <div id="abbr_help"
                 class="form-text">{!! __('learning.demonstrations.abbr.description') !!}</div>
        </div>
    </div>

    <div x-data="{ selectedTab: @if(count($assessments) == 0) 'skills' @else 'summary' @endif }" class="my-4">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link"
                   :class="selectedTab === 'summary' ? 'active' : ''"
                   x-bind:aria-current="selectedTab === 'summary' ? 'page' : null"
                   href="#"
                   @click.prevent="selectedTab = 'summary'"
                >{{ __('learning.demonstrations.assessment.summary') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   x-bind:aria-current="selectedTab === 'skills' ? 'page' : null"
                   :class="selectedTab === 'skills' ? 'active' : ''"
                   href="#"
                   @click.prevent="selectedTab = 'skills'"
                >{{ __('learning.demonstrations.skills') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   :class="selectedTab === 'rubrics' ? 'active' : ''"
                   x-bind:aria-current="selectedTab === 'rubrics' ? 'page' : null"
                   href="#"
                   @click.prevent="selectedTab = 'rubrics'"
                >{{ __('learning.demonstrations.rubrics') }}</a>
            </li>
            @if($canUseAI)
            <li class="nav-item">
                <livewire:ai.run-model-prompt
                        :model="$ld" property="skills"
                        classes="m-2 p-2 border rounded text-bg-light"
                        btn-classes="nav-link"
                        teleport-to="#skills-ai-results"
                />
            </li>
            @endif
        </ul>
        <div id="skills-ai-results"></div>
        <div class="tab-content border-end border-start border-bottom rounded pt-3">
            <div class="tab-pane fade" role="tabpanel" :class="selectedTab === 'summary' ? 'show active' : ''">
                <ul class="list-group list-group-flush">
                    @foreach($assessments as $assessment)
                        <li class="list-group-item">
                            <p class="fw-bolder mb-2 h5">{{ __('subjects.skills.assessing', ['skill' => $assessment['name']]) }}</p>
                            <p>
                                <strong>{{ __('subjects.skills.rubric.builder.criteria.assessing') }}:</strong>
                                {!! implode(", ", $assessment['rubric']->criteria) !!}
                            </p>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="tab-pane fade" role="tabpanel" :class="selectedTab === 'skills' ? 'show active' : ''">
                @error('skills')
                <div class="alert alert-danger my-2">{{ $message }}</div>
                @enderror
                <livewire:assessment.skill-selector
                        :course="$ld->course"
                        wire:model="skills"
                        wire:key="skills-{{ time() }}"
                />
            </div>
            <div class="tab-pane fade" role="tabpanel" :class="selectedTab === 'rubrics' ? 'show active' : ''">
                @foreach($assessments as $skillId => $assessment)
                    <div class="card mb-3" wire:key="assessment-{{ $assessment['id'] }}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title flex-grow-1">{{ $assessment['name'] }}</div>
                            <div class="input-group me-3 w-25">
                                <span class="input-group-text">{{ __('learning.demonstrations.skills.weight') }}: </span>
                                <input
                                        type="number"
                                        class="form-control"
                                        wire:model="assessments.{{ $skillId }}.weight"
                                />
                            </div>
                        </div>
                        <div class="card-body">
                            <livewire:subject-matter.learning.learning-demonstration-rubric-selector
                                    :rubric="$assessment['original_rubric']"
                                    wire:model="assessments.{{ $skillId }}.rubric"
                                    wire:key="rubrics-{{ $skillId }}-{{ time() }}"
                            />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-content-center">
            <h4 class="card-title">{{ __('learning.demonstrations.demonstration') }}</h4>
            @if($canUseAI)
                <div class="d-flex justify-content-center">
                    <livewire:ai.run-model-prompt
                            :model="$ld" property="demonstration"
                            classes="m-2 p-2 border rounded text-bg-light"
                            btn-classes="btn btn-sm btn-light border"
                            teleport-to="#objective-ai-results"
                    />
                </div>
            @endif
        </div>
        <div class="card-body">
            <div id="objective-ai-results"></div>
            @error('demonstration')
            <div class="alert alert-danger my-2">{{ $message }}</div>
            @enderror
            <div class="row mb-3">
                <div class="col-md-9">
                    <livewire:utilities.text-editor instance-id="demonstration" :fileable="$ld" wire:model="demonstration"/>
                </div>
                <div class="col-md-3">
                    <livewire:storage.work-storage-browser :fileable="$ld" :title="__('learning.demonstrations.files')" />
                </div>
            </div>
        </div>
    </div>

    <div class="row my-3">
        <div class="col-md-6">
            <livewire:subject-matter.learning.learning-demonstration-url-editor
                    :learning-demonstration="$ld" wire:model="links" wire:key="links-{{ time() }}" />
        </div>
        <div class="col-md-6">
            <livewire:subject-matter.learning.learning-demonstration-questions-editor
                :learning-demonstration="$ld"
                wire:model="questions"
                wire:key="questions-{{ time() }}"
            />
        </div>
    </div>

    <h3 class="border-bottom my-3">
        {{ __('learning.demonstrations.options') }}
    </h3>
    <div class="row row-cols-md-3">
        <div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" wire:model="allow_rating" switch
                       aria-describedby="allow_rating_help" id="allow_rating">
                <label class="form-check-label" for="allow_rating">
                    {{ __('learning.demonstrations.allow_rating') }}
                </label>
            </div>
            <div id="allow_rating_help"
                 class="form-text">{{ __('learning.demonstrations.allow_rating.description') }}</div>
        </div>
        <div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="online_submission" wire:model="online_submission" switch
                       aria-describedby="online_submission_help">
                <label class="form-check-label" for="online_submission">
                    {{ __('learning.demonstrations.online_submission') }}
                </label>
            </div>
            <div id="online_submission_help"
                 class="form-text">{{ __('learning.demonstrations.online_submission.description') }}</div>
        </div>
        <div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="open_submission" wire:model="open_submission" switch
                       aria-describedby="open_submission_help">
                <label class="form-check-label" for="open_submission">
                    {{ __('learning.demonstrations.open_submission') }}
                </label>
            </div>
            <div id="open_submission_help"
                 class="form-text">{{ __('learning.demonstrations.open_submission.description') }}</div>
        </div>
        <div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="submit_after_due" wire:model="submit_after_due" switch
                       aria-describedby="submit_after_due_help">
                <label class="form-check-label" for="submit_after_due">
                    {{ __('learning.demonstrations.submit_after_due') }}
                </label>
            </div>
            <div id="submit_after_due_help"
                 class="form-text">{{ __('learning.demonstrations.submit_after_due.description') }}</div>
        </div>
        <div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="share_submissions" wire:model="share_submissions" switch
                       aria-describedby="share_submissions_help">
                <label class="form-check-label" for="share_submissions">
                    {{ __('learning.demonstrations.share_submissions') }}
                </label>
            </div>
            <div id="share_submissions_help"
                 class="form-text">{{ __('learning.demonstrations.share_submissions.description') }}</div>
        </div>
    </div>
    <div class="border-bottom d-flex justify-content-start mt-5" x-data>
        <h2 class="text-nowrap flex-grow-1 me-3">
            {{ __('learning.demonstrations.post') }}
        </h2>
        <div class="flex-shrink-1 me-3">
            <div class="input-group">
                <span class="input-group-text">{{ __('learning.demonstrations.post.on') }}</span>
                <input
                        type="datetime-local"
                        class="form-control"
                        x-ref="post_on_default"
                />
                <button
                    class="btn btn-primary"
                    wire:click="applyDefaultPost($refs.post_on_default.value)"
                >{{ __('common.apply.all') }}</button>
            </div>
        </div>
        <div class="flex-shrink-1">
            <div class="input-group">
                <span class="input-group-text">{{ __('learning.demonstrations.due.on') }}</span>
                <input
                        type="datetime-local"
                        class="form-control"
                        x-ref="due_on_default"
                />
                <button
                        class="btn btn-primary"
                        wire:click="applyDefaultDue($refs.due_on_default.value)"
                >{{ __('common.apply.all') }}</button>
            </div>
        </div>
    </div>
    <ul class="list-group">
    @foreach($courseClasses as $class_id => $class)
        <li
            class="list-group-item p-0"
            x-data="{ enabled: $wire.entangle('courseClasses.{{ $class_id }}.selected') }"
            wire:key="post_classes_{{ $class_id }}"
        >
            <div class="row align-items-center p-2">
                <div class="col-md-9 d-flex align-items-start">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                            wire:model="courseClasses.{{ $class_id }}.selected">
                    </div>
                    <h5 class="ms-3">{{ $class['class'] }}</h5>
                </div>
                <div class="col-md-3 text-end">
                    <button
                            type="button"
                            class="btn {{ $class['post_to_class']? 'btn-primary': 'btn-warning' }}"
                            wire:click="toggleCoursePostStudents({{ $class_id }})"
                            :disabled="!enabled"
                    >
                        {{ $class['post_to_class']? __('learning.demonstrations.post.students'): __('learning.demonstrations.post.class') }}
                    </button>
                </div>
            </div>
            <div class="input-group">
                <span class="input-group-text">{{ __('learning.criteria') }}:</span>
                <select class="form-select" wire:model="courseClasses.{{ $class_id }}.criteria_id" :disabled="!enabled">
                    @foreach($class['criteria'] as $criteriaId => $criteriaName)
                        <option value="{{ $criteriaId }}">{{ $criteriaName }}</option>
                    @endforeach
                </select>
                <span class="input-group-text">{{ __('learning.demonstrations.skills.weight') }}:</span>
                <input
                        class="form-control"
                        type="number"
                        step="0.1"
                        min="0"
                        :disabled="!enabled"
                        wire:model="courseClasses.{{ $class_id }}.criteria_weight"
                />
                <span class="input-group-text">{{ __('learning.demonstrations.post.on') }}</span>
                <input
                        type="datetime-local"
                        class="form-control"
                        :disabled="!enabled"
                        wire:model="courseClasses.{{ $class_id }}.post"
                />
                <label class="input-group-text">{{ __('learning.demonstrations.due.on') }}</label>
                <input
                        type="datetime-local"
                        class="form-control"
                        :disabled="!enabled"
                        wire:model="courseClasses.{{ $class_id }}.due"
                />
            </div>
            @if(!$class['post_to_class'])
            <div class="m-0 p-2 border-top text-bg-light">
                <h5 class="mb-3">{{ __('learning.demonstrations.post.students.only') }}</h5>
                <div class="d-flex flex-wrap">
                    @foreach($class['students'] as $student_id => $student)
                        <div class="form-check m-2 border-end pe-2" wire:key="student_{{ $student_id }}">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                wire:model="courseClasses.{{ $class_id }}.students.{{ $student_id }}.selected"
                                id="student_{{ $student_id }}"
                            />
                            <label class="form-check-label" for="student_{{ $student_id }}">
                                {{ $student['student'] }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </li>
    @endforeach
    </ul>
    <div class="mt-3" x-data="{ expanded: false }">
        <div class="border-bottom my-3 d-flex justify-content-between align-items-center">
            <h4>{{ __('learning.demonstrations.post.other') }}</h4>
            <a class="show-as-action" @click="expanded = !expanded" x-html="expanded ? '[{{ __('collapse') }}]' : '[{{ __('expand') }}]'"></a>
        </div>
        <div x-cloak x-show="expanded">
            @foreach($otherClasses as $course_id => $course)
                <div class="card mb-3" wire:key="post_other_container_{{ $course_id }}">
                    <h5 class="card-header">{{ $course['course'] }}</h5>
                    <ul class="list-group list-group-flush">
                        @foreach($course['classes'] as $class_id => $class)
                            <li
                                class="list-group-item p-0"
                                x-data="{ enabled: $wire.entangle('otherClasses.{{ $course_id }}.classes.{{ $class_id }}.selected') }"
                                wire:key="post_other_{{ $course_id }}_{{ $class_id }}"
                            >
                                <div class="row align-items-center p-2">
                                    <div class="col-md-9 d-flex align-items-start">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="otherClasses.{{ $course_id }}.classes.{{ $class_id }}.selected">
                                        </div>
                                        <h5 class="ms-3">{{ $class['class'] }}</h5>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <button
                                                type="button"
                                                class="btn btn-sm {{ $class['post_to_class']? 'btn-primary': 'btn-warning' }}"
                                                wire:click="toggleOtherPostStudents({{ $course_id }}, {{ $class_id }})"
                                                :disabled="!enabled"
                                        >
                                            {{ $class['post_to_class']? __('learning.demonstrations.post.students'): __('learning.demonstrations.post.class') }}
                                        </button>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">{{ __('learning.criteria') }}:</span>
                                    <select
                                        class="form-select"
                                        wire:model="otherClasses.{{ $course_id }}.classes.{{ $class_id }}.criteria_id"
                                        :disabled="!enabled"
                                    >
                                        @foreach($class['criteria'] as $criteriaId => $criteriaName)
                                            <option value="{{ $criteriaId }}">{{ $criteriaName }}</option>
                                        @endforeach
                                    </select>
                                    <span class="input-group-text">{{ __('learning.demonstrations.skills.weight') }}:</span>
                                    <input
                                            class="form-control"
                                            type="number"
                                            step="0.1"
                                            min="0"
                                            :disabled="!enabled"
                                            wire:model="otherClasses.{{ $course_id }}.classes.{{ $class_id }}.criteria_weight"
                                    />
                                    <span class="input-group-text">{{ __('learning.demonstrations.post.on') }}</span>
                                    <input
                                            type="datetime-local"
                                            class="form-control"
                                            :disabled="!enabled"
                                            wire:model="otherClasses.{{ $course_id }}.classes.{{ $class_id }}.post"
                                    />
                                    <label class="input-group-text">{{ __('learning.demonstrations.due.on') }}</label>
                                    <input
                                            type="datetime-local"
                                            class="form-control"
                                            :disabled="!enabled"
                                            wire:model="otherClasses.{{ $course_id }}.classes.{{ $class_id }}.due"
                                    />
                                </div>
                                @if(!$class['post_to_class'])
                                    <div class="m-0 p-2 border-top text-bg-light">
                                        <h5 class="mb-3">{{ __('learning.demonstrations.post.students.only') }}</h5>
                                        <div class="d-flex flex-wrap">
                                            @foreach($class['students'] as $student_id => $student)
                                                <div class="form-check m-2 border-end pe-2" wire:key="student_{{ $student_id }}">
                                                    <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            wire:model="otherClasses.{{ $course_id }}.classes.{{ $class_id }}.students.{{ $student_id }}.selected"
                                                            id="student_{{ $student_id }}"
                                                    />
                                                    <label class="form-check-label" for="student_{{ $student_id }}">
                                                        {{ $student['student'] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
    <div class="row mt-5">
        <button
            type="button"
            class="col mx-2 btn btn-primary"
            wire:click="post"
        >{{ __('learning.demonstrations.post.save') }}</button>
        <button
            type="button"
            class="col mx-2 btn"
            :class="saved? 'btn-success' : 'btn-info'"
            wire:click="updateTemplate"
            x-data="{ saved: false }"
            x-html="saved? '{{ __('learning.demonstrations.saved') }}' : '{{ __('learning.demonstrations.save') }}'"
            x-on:template-updated.window="saved = true; setTimeout(() => saved = false, 3000)"
        ></button>
    </div>
</div>
