<div class="row border rounded text-bg-secondary p-2" style="height: 600px; overflow: hidden">
    <div class="col-sm-6 col-md-5 col-lg-4 position-relative">
        <div style="height: 120px;">
            <div class="row row-cols-auto mb-2">
                <button
                        type="button"
                        class="btn btn-primary btn-sm col"
                        wire:click="createCategory()"
                >
                    <i class="fa-solid fa-plus border-end pe-2 me-2"></i>
                    {{ trans_choice('subjects.skills.category',1) }}
                </button>
            </div>

            <div class="row row-cols-auto mb-3">
                <a
                        role="button"
                        href="{{ $selectedCategory? route('subjects.skills.create.knowledge', $selectedCategory): '#' }}"
                        class="btn btn-success btn-sm me-2 col {{ $selectedCategory? '': 'disabled' }}"
                >
                    <i class="fa-solid fa-plus border-end pe-1 me-1"></i>
                    {{ trans_choice('subjects.skills.knowledge',1) }}
                </a>

                <a
                        role="button"
                        href="{{ $selectedCategory? route('subjects.skills.create.character', $selectedCategory): '#' }}"
                        class="btn btn-warning btn-sm col {{ $selectedCategory? '': 'disabled' }}"
                >
                    <i class="fa-solid fa-plus border-end pe-2 me-2"></i>
                    {{ trans_choice('subjects.skills.character',1) }}
                </a>
            </div>
            <div class="fw-bolder fs-5 border-bottom mb-3">Categories</div>
        </div>
        <ul
                class="list-group list-group-flush flex-grow-1"
                style="height: 470px; overflow: auto;"
                x-data="{dragging: false}"
        >
            @foreach($rootCategories as $category)
                <livewire:assessment.category-item
                        :category="$category"
                        depth="0"
                        :key="$category->id"
                        classes="text-bg-secondary"
                        :open-to="$openTo"
                />
            @endforeach
        </ul>
    </div>
    <div class="col-sm-6 col-md-7 col-lg-8">
        <div class="input-group mb-3" style="height: 35px;">
            <span for="search-skills" class="input-group-text">{{ __('subjects.skills.search') }}</span>
            <input
                    type="text"
                    id="search-skills"
                    class="form-control"
                    wire:model.live.debounce.400ms="search"
                    placeholder="{{ __('subjects.skills.search') }}"
            />
        </div>
        @if($knowledgeResults || $characterResults)
            <div class="selected-category-container" style="height: 540px; overflow: auto;">
                @if($knowledgeResults->count() > 0)
                    <div class="fw-bolder fs-5 border-bottom mb-2">
                        {{ trans_choice('subjects.skills.knowledge',2) }}
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($knowledgeResults as $skill)
                                    <a
                                            class="list-group-item list-group-item-action @if($skill->active) list-group-item-success @else list-group-item-danger @endif"
                                            href="{{ route('subjects.skills.show.knowledge', $skill) }}"
                                    >{{ $skill->designation . ($skill->name? " (" . $skill->name . ")": '') }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                @if($characterResults->count() > 0)
                    <div class="fw-bolder fs-5 border-bottom mb-2">
                        {{ trans_choice('subjects.skills.character',2) }}
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($characterResults as $skill)
                                    <a
                                            class="list-group-item list-group-item-action @if($skill->active) list-group-item-warning @else list-group-item-danger @endif"
                                            href="{{ route('subjects.skills.show.character', $skill) }}"
                                    >{{ $skill->designation . ($skill->name? " (" . $skill->name . ")": '') }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @elseif($selectedCategory)
            <div class="selected-category-container" style="height: 540px; overflow: auto;">
                @if($selectedCategory->knowledgeSkills()->count() > 0)
                    <div class="fw-bolder fs-5 border-bottom mb-2">
                        {{ trans_choice('subjects.skills.knowledge',$selectedCategory->knowledgeSkills()->count()) }}
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($selectedCategory->knowledgeSkills as $skill)
                                    <a
                                            class="list-group-item list-group-item-action @if($skill->active) list-group-item-success @else list-group-item-danger @endif"
                                            href="{{ route('subjects.skills.show.knowledge', $skill) }}"
                                    >{{ $skill->designation . ($skill->name? " (" . $skill->name . ")": '') }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                @if($selectedCategory->characterSkills()->count() > 0)
                    <div class="fw-bolder fs-5 border-bottom mb-2">
                        {{ trans_choice('subjects.skills.character',$selectedCategory->characterSkills()->count()) }}
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($selectedCategory->characterSkills as $skill)
                                    <a
                                            class="list-group-item list-group-item-action @if($skill->active) list-group-item-warning @else list-group-item-danger @endif"
                                            href="{{ route('subjects.skills.show.character', $skill) }}"
                                    >{{ $skill->designation . ($skill->name? " (" . $skill->name . ")": '') }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@script
<script>
    $wire.on('select-category', function (category) {
        $('.category-container[category_id!=' + category.selectedCategoryId + ']').removeClass('bg-primary-subtle');
    });
</script>
@endscript
