<li class="list-group-item list-group-action text-bg-secondary" style="padding: 0 0 0 0.3em;">
    <div
            class="category-container d-flex justify-content-start align-items-center rounded @if($selected) bg-primary-subtle @endif p-2"
            droppable="true"
            category_id="{{ $category->id }}"
    >
        @if($numChildren > 0)
            <div class="me-2">
                @if($open)
                    <i class="fa-solid fa-caret-down show-as-action" wire:click="toggleCategory()"></i>
                @else
                    <i class="fa-solid fa-caret-right show-as-action" wire:click="toggleCategory()"></i>
                @endif
            </div>
        @endif
        <div class="flex-grow-1">
            <div
                    class="show-as-action"
                    wire:click="selectCategory()"
            >
                {{ $category->name }}

                @if($category->skills()->count() > 0)
                    <span class="badge text-bg-success ms-3">{{ $category->skills()->count() }}</span>
                @endif
            </div>
        </div>
    </div>
    <ul class="list-group list-group-flush @if(!$open) d-none @endif border-start border-black">
        @if($children)
            @foreach($children as $child)
                <livewire:assessment.skill-selector-category
                        :category="$child"
                        wire:key="subcat-{{ $child->id }}"
                />
            @endforeach
        @endif
    </ul>
</li>
