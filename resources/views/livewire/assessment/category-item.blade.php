<li class="list-group-item list-group-action {{ $classes }}" style="padding: 0 0 0 0.3em;">
    <div
        class="category-container d-flex justify-content-start align-items-center rounded @if($selected) bg-primary-subtle @endif p-2"
        droppable="true"
        categor_id="{{ $category->id }}"
        x-on:dragover.prevent="$event.target.classList.add('bg-warning-subtle');$event.dataTransfer.effectAllowed = 'move';"
        x-on:dragleave="$event.target.classList.remove('bg-warning-subtle')"
        x-on:dragstart=" $event.dataTransfer.setData('text/plain', {{ $category->id }})"
        wire:drop.prevent="$dispatchTo('assessment.skill-category-browser', 'move-category',
                { categoryId: $event.dataTransfer.getData('text/plain'), parentId: {{ $category->id }} });"
    >
        @if(!$children || $children->count() > 0)
        <div class="me-2">
            @if($open)
                <i class="fa-solid fa-caret-down show-as-action" wire:click="toggleCategory()"></i>
            @else
                <i class="fa-solid fa-caret-right show-as-action" wire:click="toggleCategory()"></i>
            @endif
        </div>
        @endif
        <div class="flex-grow-1">
            @if($editing)
                <div class="input-group">
                    <div class="form-floating">
                        <input
                            type="text"
                            class="form-control"
                            id="name-{{ $category->id }}"
                            placeholder="Category Name"
                            wire:model="name"
                            wire:change="updateName()"
                        />
                        <label for="floatingInput">Category Name</label>
                    </div>
                    <button
                        class="btn btn-secondary"
                        wire:click="$dispatchTo('assessment.skill-category-browser', 'edit-category', { editCategoryId: null})"
                    ><i class="fa-solid fa-times"></i></button>
                </div>
            @else
                <div
                    class="show-as-action"
                    wire:click="selectCategory()"
                >
                    {{ $category->name }}

                    @if($category->knowledgeSkills()->count() > 0)
                        <span class="badge text-bg-success ms-3">{{ $category->knowledgeSkills()->count() }}</span>
                    @endif
                    @if($category->characterSkills()->count() > 0)
                        <span class="badge text-bg-warning ms-1">{{ $category->characterSkills()->count() }}</span>
                    @endif
                </div>
            @endif
        </div>
        @if(!$editing)
            <i
                class="fa-solid fa-edit mx-1 text-info show-as-action"
                wire:click="$dispatchTo('assessment.skill-category-browser', 'edit-category', { editCategoryId: {{ $category->id }} })"
            ></i>
            @if($category->canDelete())
            <i
                class="fa-solid fa-times mx-1 text-danger show-as-action"
                wire:confirm="{{ __('subjects.skills.category.delete.confirm') }}"
                wire:click="removeCategory()"
            ></i>
            @endif
            <i
                class="fa-solid fa-arrows-up-down-left-right mx-1 show-as-grab"
                x-on:mousedown="$event.target.parentNode.setAttribute('draggable', 'true')"
                x-on:mouseup="$event.target.parentNode.setAttribute('draggable', 'false')"
            ></i>
        @endif
    </div>
    <ul class="list-group list-group-flush @if(!$open) d-none @endif border-start border-black">
        @if($children)
        @foreach($children as $child)
            <livewire:assessment.category-item
                :category="$child"
                depth="{{ $depth + 1 }}"
                :key="$child->id"
                :classes="$classes"
                :open-to="$openTo"
            />
        @endforeach
        @endif
    </ul>
</li>
