<div class="input-group">
    <span class="input-group-text">{{ trans_choice('subjects.skills.category', 1) }}</span>
    <select class="form-select" wire:model="selectedRootCategoryId" wire:change="selectRootCategory">
        @foreach($rootCategories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>
    @foreach($childrenCategories as $category)
        <select class="form-select" wire:change="selectChildCategory({{ $loop->iteration }}, $event.target.value)">
            @foreach($category as $children)
                <option value="{{ $children->id }}">{{ $children->name }}</option>
            @endforeach
        </select>
    @endforeach
    <select class="form-select" wire:model="selectedLatestCategoryId" wire:change="selectLatestCategory()">
        <option value="0">{{ __('subjects.skills.category.select') }}</option>
        @foreach($latestCategories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>
    <input type="hidden" name="category_id" id="category_id" value="{{ $selectedCategoryId }}" />
</div>
