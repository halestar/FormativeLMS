<div class="card text-bg-secondary p-2" style="height: 600px; overflow: hidden">
    <div class="card-header">
        <div class="d-flex justify-content-start align-items-end">
            <div class="fw-bold fs-6 me-3">{{ trans_choice('subjects.skills.level',2) }}:</div>
            <div class="flex-grow-1 d-flex justify-content-start align-items-end flex-wrap">
                @foreach($levels as $level)
                    <div class="form-check mx-2" wire:key="level-{{ $level->id }}">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="level_{{ $level->id }}"
                            wire:model="filterLevels"
                            value="{{ $level->id }}"
                            name="levels[]"
                            wire:click="$refresh()"
                        >
                        <label class="form-check-label" for="level_{{ $level->id }}">
                            {{ $level->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="form-check ms-4">
                <input
                        class="form-check-input"
                        type="checkbox"
                        id="level_all"
                        wire:click="toggleAllLevels($event.target.checked)"
                >
                <label class="form-check-label" for="level_all">
                    {{ __('common.toggle.all') }}
                </label>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row border-bottom mb-3">
            <div class="col-lg-3 align-self-center fw-bolder fs-5">
                {{ trans_choice('common.category',2) }}
            </div>
            <div class="col-lg-6 align-self-end">
                <div class="input-group mb-3 align-self-end">
                    <span for="search-skills" class="input-group-text">{{ __('subjects.skills.search') }}</span>
                    <input
                        type="text"
                        id="search-skills"
                        class="form-control"
                        wire:model.live.debounce.400ms="search"
                        placeholder="{{ __('subjects.skills.search') }}"
                        @keydown.enter.prevent=""
                    />
                </div>
            </div>
            <div class="col-lg-3 align-self-center text-center fw-bolder fs-5">
                {{ trans_choice('subjects.skills.selected', 2) }}
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 position-relative" style="height: 470px; overflow: auto;">
                <ul class="list-group list-group-flush flex-grow-1">
                    <li
                            class="list-group-item list-group-action show-as-action rounded @if($selected == "suggested") bg-primary-subtle @else text-bg-secondary @endif p-2"
                            wire:click="setSuggested"
                    >
                        {{ __('subjects.skills.suggested') }}
                    </li>
                    <li
                            class="list-group-item list-group-action rounded show-as-action  @if($selected == "subject") bg-primary-subtle @else text-bg-secondary @endif p-2"
                            wire:click="setSubject"
                    >
                        {{ $course->subject->name }}
                    </li>
                    <li
                            class="list-group-item list-group-action rounded show-as-action  @if($selected == "global") bg-primary-subtle @else text-bg-secondary @endif p-2"
                            wire:click="setGlobal"
                    >
                        {{ trans_choice('subjects.skills.global',2) }}
                    </li>
                    <li class="list-group-item list-group-action text-bg-secondary" style="padding: 0 0 0 0.3em;">

                        <div class="category-container d-flex justify-content-start align-items-center rounded p-2">

                            <div class="me-2">
                                <i class="fa-solid fa-caret-down show-as-action"></i>
                            </div>
                            <div class="flex-grow-1 show-as-action">
                                {{ __('subjects.skills.all') }}
                            </div>
                        </div>
                        <ul class="list-group list-group-flush border-start border-black">
                            @foreach($rootCategories as $rootCategory)
                                <livewire:assessment.skill-selector-category
                                        :category="$rootCategory"
                                        wire:key="cat-{{ $rootCategory->id }}"
                                />
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6" style="height: 470px; overflow: auto;">
                @if($results && $results->count() > 0)
                    <div class="card mb-3">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @foreach($results as $skill)
                                    <li class="list-group-item list-group-item-action list-group-item-primary" wire:key="skill-result-{{ $skill->id }}">
                                        <h5 class="border-bottom d-flex justify-content-between align-items-center">
                                        <span>
                                            {{ $skill->designation . ($skill->name? " (" . $skill->name . ")": '') }}
                                        </span>
                                            <span class="text-sm">{{ $skill->levels->pluck('name')->join(', ') }}</span>
                                            <a class="link-success">
                                                <i class="fa-solid fa-circle-right" wire:click="addSkill({{ $skill->id }})"></i>
                                            </a>
                                        </h5>
                                        <p
                                                class="mx-1 text-wrap position-relative pe-4"
                                                x-data="{ expanded: false }"
                                                x-show="expanded"
                                                x-collapse.min.50px
                                        >
                                            {!! $skill->description !!}
                                            <a
                                                    class="position-absolute bottom-0 end-0"
                                                    @click="expanded = !expanded"
                                            >
                                                <i class="text-dark bi"
                                                   :class="expanded ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                                            </a>
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-lg-3" style="height: 470px; overflow: auto;">
                @foreach($selectedSkills as $skill)
                    <div class="card mb-3 text-bg-success" wire:key="skill-selected-{{ $skill->id }}">
                        <h5 class="border-bottom d-flex justify-content-between align-items-center card-header">
                            <a class="link-danger me-2" wire:click="removeSkill({{ $skill->id }})">
                                <i class="fa-solid fa-circle-left"></i>
                            </a>
                            <span class="flex-grow-1">
                                {{ $skill->designation . ($skill->name? " (" . $skill->name . ")": '') }}
                            </span>
                            <span class="text-sm text-wrap">{{ $skill->levels->pluck('name')->join(', ') }}</span>
                        </h5>
                        <p
                                class="mx-1 text-wrap position-relative pe-4 card-body"
                                x-data="{ expanded: false }"
                                x-show="expanded"
                                x-collapse.min.50px
                        >
                            {!! $skill->description !!}
                            <a
                                    class="position-absolute bottom-0 end-0"
                                    @click="expanded = !expanded"
                            >
                                <i class="text-dark bi"
                                   :class="expanded ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                            </a>
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
