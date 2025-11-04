<div class="card text-bg-secondary p-2" style="height: 600px; overflow: hidden">
    <div class="card-header">
        <div class="d-flex justify-content-start align-items-end">
            <div class="fw-bold fs-6 me-3">{{ trans_choice('subjects.skills.level',2) }}:</div>
            <div class="flex-grow-1 d-flex justify-content-start align-items-end flex-wrap">
                @foreach($levels as $level)
                    <div class="form-check mx-2" wire:key="{{ $level->id }}">
                        <input
                                class="form-check-input"
                                type="checkbox"
                                id="level_{{ $level->id }}"
                                wire:model="filterLevels"
                                value="{{ $level->id }}"
                                wire:click="$refresh()"
                        >
                        <label class="form-check-label" for="level_{{ $level->id }}">
                            {{ $level->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="card-body row">
        <div class="col-lg-6 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="fw-bolder fs-5 border-bottom mb-3">
                    {{ trans_choice('common.category',2) }}
                </div>
                <div>
                    <button
                            type="button"
                            class="btn btn-primary btn-sm col mx-2"
                            wire:click="createCategory()"
                    >
                        <i class="fa-solid fa-plus border-end pe-2 me-2"></i>
                        {{ trans_choice('subjects.skills.category', 1) }}
                    </button>
                    <a
                            role="button"
                            href="{{ route('subjects.skills.create', $selectedCategory) }}"
                            class="btn btn-success btn-sm me-2 col mx-2"
                    >
                        <i class="fa-solid fa-plus border-end pe-1 me-1"></i>
                        {{ trans_choice('subjects.skills', 1) }}
                    </a></div>
            </div>
            <ul
                    class="list-group list-group-flush flex-grow-1"
                    style="max-height: 450px; overflow: auto;"
                    x-data="{dragging: false}"
            >
                <li
                        class="list-group-item list-group-action rounded show-as-action  @if(!$selectedCategory) bg-primary-subtle @else text-bg-secondary @endif p-2"
                        wire:click="dispatch('select-category', {selectedCategoryId: null})"
                >
                    {{ trans_choice('subjects.skills.global',2) }}

                </li>
                @foreach($rootCategories as $category)
                    <livewire:assessment.category-item
                            :category="$category"
                            depth="0"
                            :key="$category->id"
                            classes="text-bg-secondary"
                            :open-to="$openTo"
                            wire:key="{{$category->id}}"
                    />
                @endforeach
            </ul>
        </div>
        <div class="col-lg-6">
            <div class="input-group mb-3">
                <span for="search-skills" class="input-group-text">{{ __('subjects.skills.search') }}</span>
                <input
                        type="text"
                        id="search-skills"
                        class="form-control"
                        wire:model.live.debounce.400ms="search"
                        placeholder="{{ __('subjects.skills.search') }}"
                />
            </div>
            @if($skills && $skills->count() > 0)
                <div class="selected-category-container" style="height: 450px; overflow: auto;">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($skills as $skill)
                                    <a
                                        class="list-group-item list-group-item-action @if($skill->active) list-group-item-success @else list-group-item-danger @endif d-flex justify-content-between align-items-center"
                                        href="{{ route('subjects.skills.show', $skill) }}"
                                    >
                                        <span class="fw-bold">{{ $skill->designation . ($skill->name? " (" . $skill->name . ")": '') }}</span>
                                        <div class="text-end">{{ $skill->levels->pluck('name')->join(', ') }}</div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        </div>
                </div>
            @elseif($selectedCategory && $selectedCategory->skills()->forLevels($filterLevels)->count() > 0)
                <div class="selected-category-container" style="height: 450px; overflow: auto;">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($selectedCategory->skills()->forLevels($filterLevels)->get() as $skill)
                                    <a
                                            class="list-group-item list-group-item-action @if($skill->active) list-group-item-success @else list-group-item-danger @endif d-flex justify-content-between align-items-center"
                                            href="{{ route('subjects.skills.show', $skill) }}"
                                    >
                                        <span class="fw-bold">{{ $skill->designation . ($skill->name? " (" . $skill->name . ")": '') }}</span>
                                        <div class="text-end">{{ $skill->levels->pluck('name')->join(', ') }}</div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(!$selectedCategory)
                <div class="selected-category-container" style="height: 450px; overflow: auto;">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach(\App\Models\SubjectMatter\Assessment\Skill::global()->forLevels($filterLevels)->get() as $skill)
                                    <a
                                            class="list-group-item list-group-item-action @if($skill->active) list-group-item-success @else list-group-item-danger @endif d-flex justify-content-between align-items-center"
                                            href="{{ route('subjects.skills.show', $skill) }}"
                                    >
                                        <span class="fw-bold">{{ $skill->designation . ($skill->name? " (" . $skill->name . ")": '') }}</span>
                                        <div class="text-end">{{ $skill->levels->pluck('name')->join(', ') }}</div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@script
<script>
    $wire.on('select-category', function (category) {
        $('.category-container[category_id!=' + category.selectedCategoryId + ']').removeClass('bg-primary-subtle');
    });
</script>
@endscript
