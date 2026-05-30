<div class="card shadow-sm h-100" x-data="{ showCategories: false, showFilters: false }">
    <div class="card-header bg-light border-bottom py-2 px-3">
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold fs-6 text-muted mb-0">
                {{ trans_choice('subjects.skills.level',2) }}
                <span x-show="!showFilters">: {{ $this->filteredLevels }}</span>
            </span>
            <button type="button" class="btn btn-sm btn-outline-secondary" @click="showFilters = !showFilters">
                <i class="fa-solid fa-filter"></i> <span class="d-none d-sm-inline">{{ __('Filter') }}</span> <i
                        class="fa-solid fa-chevron-down ms-1" x-show="!showFilters"></i><i
                        class="fa-solid fa-chevron-up ms-1" x-show="showFilters" x-cloak></i>
            </button>
        </div>
        <div x-show="showFilters" x-collapse>
            <div class="d-flex align-items-center flex-wrap gap-3 mt-3 pb-2">
                @foreach($levels as $level)
                    <div class="form-check form-switch m-0" wire:key="level-{{ $level->id }}">
                        <input
                                class="form-check-input"
                                style="cursor: pointer;"
                                type="checkbox"
                                role="switch"
                                id="level_{{ $level->id }}"
                                wire:model="filterLevels"
                                value="{{ $level->id }}"
                                name="levels[]"
                                wire:click="$refresh()"
                        >
                        <label class="form-check-label small" style="cursor: pointer;" for="level_{{ $level->id }}">
                            {{ $level->name }}
                        </label>
                    </div>
                @endforeach
                <div class="form-check form-switch m-0 border-start ps-4 ms-2">
                    <input
                            class="form-check-input"
                            style="cursor: pointer;"
                            type="checkbox"
                            role="switch"
                            id="level_all"
                            wire:click="toggleAllLevels($event.target.checked)"
                    >
                    <label class="form-check-label fw-medium small" style="cursor: pointer;" for="level_all">
                        {{ __('common.toggle.all') }}
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0 d-flex flex-column" style="min-height: 500px;">
        <div class="row g-0 border-bottom bg-white align-items-stretch">
            <div class="col-12 col-lg-4 border-end bg-light" x-show="showCategories" x-transition>
                <div class="p-2 fw-bold text-secondary text-uppercase fs-7 d-flex justify-content-between align-items-center h-100">
                    <span>{{ trans_choice('common.category',2) }}</span>
                    <button type="button" class="btn btn-sm btn-link text-secondary p-0"
                            @click="showCategories = false">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            </div>
            <button type="button" class="col-auto btn btn-sm btn-link text-secondary px-3"
                    @click="showCategories = true"
                    title="Show Categories"
                    x-show="!showCategories"
            >
                <i class="fa-solid fa-folder-tree"></i>
            </button>
            <div class="col border-end bg-white">
                <div class="input-group h-100">
                    <span for="search-skills" class="input-group-text bg-transparent text-muted border-0 pe-2">
                        <i class="fa-solid fa-search"></i>
                    </span>
                    <input
                            type="text"
                            id="search-skills"
                            class="form-control border-0 ps-0 shadow-none bg-transparent"
                            wire:model.live.debounce.400ms="search"
                            placeholder="{{ __('subjects.skills.search') }}"
                            @keydown.enter.prevent=""
                    />
                </div>
            </div>
            <div class="col-12 col-lg-3 p-2 d-none d-lg-flex align-items-center justify-content-center fw-bold text-secondary text-uppercase fs-7 bg-light">
                {{ trans_choice('subjects.skills.selected', 2) }}
            </div>
        </div>
        <div class="row g-0 flex-grow-1 overflow-hidden" style="height: 0; min-height: 500px;">
            <div class="col-12 col-lg-4 border-end position-relative h-100 overflow-y-auto p-2 bg-white"
                 x-show="showCategories" x-transition>
                <ul class="list-group list-group-flush border-0 small">
                    <li
                            class="list-group-item list-group-item-action show-as-action rounded mb-1 border-0 @if($selected == "suggested") bg-primary bg-opacity-10 text-primary fw-bold @else bg-light @endif p-2"
                            wire:click="setSuggested"
                    >
                        <i class="fa-solid fa-lightbulb me-2 @if($selected == "suggested") text-primary @else text-warning @endif"></i> {{ __('subjects.skills.suggested') }}
                    </li>
                    <li
                            class="list-group-item list-group-item-action show-as-action rounded mb-1 border-0 @if($selected == "subject") bg-primary bg-opacity-10 text-primary fw-bold @else bg-light @endif p-2"
                            wire:click="setSubject"
                    >
                        <i class="fa-solid fa-book me-2 @if($selected == "subject") text-primary @else text-info @endif"></i> {{ $course->subject->name }}
                    </li>
                    <li
                            class="list-group-item list-group-item-action show-as-action rounded mb-2 border-0 @if($selected == "global") bg-primary bg-opacity-10 text-primary fw-bold @else bg-light @endif p-2"
                            wire:click="setGlobal"
                    >
                        <i class="fa-solid fa-globe me-2 @if($selected == "global") text-primary @else text-success @endif"></i> {{ trans_choice('subjects.skills.global',2) }}
                    </li>
                    <li class="list-group-item p-0 border-0">
                        <div class="d-flex align-items-center p-2 mb-1 rounded bg-secondary bg-opacity-10">
                            <i class="fa-solid fa-folder-tree me-2 text-secondary"></i>
                            <span class="fw-bold text-secondary">{{ __('subjects.skills.all') }}</span>
                        </div>
                        <ul class="list-group list-group-flush bg-transparent ps-1">
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
            <div class="col border-end h-100 overflow-y-auto bg-light bg-opacity-50 p-2">
                @if($this->results->count() > 0)
                    <div class="d-flex flex-column gap-2">
                        @foreach($this->results as $skill)
                            <div class="card border-0 shadow-sm rounded-1" wire:key="skill-result-{{ $skill->id }}">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-start border-bottom pb-1 mb-1">
                                        <div class="flex-grow-1 pe-2 min-w-0">
                                            <h6 class="mb-0 fw-bold text-dark text-truncate fs-7">
                                                {{ $skill->designation }}
                                                @if($skill->name)
                                                    <span class="text-muted fw-normal">({{ $skill->name }})</span>
                                                @endif
                                            </h6>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill"
                                                  style="font-size: 0.65rem;">
                                                {{ $skill->levels->pluck('name')->join(', ') }}
                                            </span>
                                        </div>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary rounded-1 flex-shrink-0 py-0 px-2"
                                                wire:click="addSkill({{ $skill->id }})" title="Add Skill">
                                            <i class="fa-solid fa-plus small"></i>
                                        </button>
                                    </div>
                                    <div
                                            class="text-muted position-relative" style="font-size: 0.8rem;"
                                            x-data="{ expanded: false }"
                                    >
                                        <div :class="expanded ? '' : 'text-truncate'" style="max-width: 100%;">
                                            {!! $skill->description !!}
                                        </div>
                                        <button type="button"
                                                class="btn btn-link btn-sm p-0 text-decoration-none mt-1"
                                                style="font-size: 0.75rem;"
                                                @click="expanded = !expanded"
                                        >
                                            <span x-show="!expanded">{{ __('Read more') }} <i
                                                        class="fa-solid fa-chevron-down ms-1"></i></span>
                                            <span x-show="expanded" x-cloak>{{ __('Show less') }} <i
                                                        class="fa-solid fa-chevron-up ms-1"></i></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-12 col-lg-5 h-100 overflow-y-auto bg-white p-2 border-top border-lg-0">
                <div class="d-lg-none fw-bold text-secondary text-uppercase fs-7 bg-light p-2 mb-2 rounded text-center">
                    {{ trans_choice('subjects.skills.selected', 2) }}
                </div>
                @if($selectedSkills && count($selectedSkills) > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($selectedSkills as $skill)
                            <li class="list-group-item"
                                wire:key="skill-selected-{{ $skill->id }}"
                                x-data="{ expanded: false }"
                            >
                                <div class="d-flex justify-content-between align-items-center pb-1 mb-1">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger me-2 flex-shrink-1"
                                            wire:click="removeSkill({{ $skill->id }})" title="Remove Skill">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="mb-0 fw-bold text-success text-truncate fs-7">
                                            {{ $skill->prettyName() }}
                                        </h6>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill"
                                              style="font-size: 0.65rem;">
                                                {{ $skill->levels->pluck('name')->join(', ') }}
                                            </span>
                                    </div>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            x-on:click="expanded = !expanded"
                                    >
                                        <i class="fa-solid fa-circle-chevron-down" x-show="expanded" x-cloak></i>
                                        <i class="fa-solid fa-circle-chevron-up" x-show="!expanded" x-cloak></i>
                                    </button>
                                </div>
                                <div
                                        class="text-muted position-relative border-top border-success border-opacity-25 "
                                        style="font-size: 0.8rem;"
                                        x-show="expanded"
                                        x-transition
                                >

                                    {!! $skill->description !!}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
