<?php

use App\Models\SystemTables\SystemTable;
use App\Models\SystemTables\SystemTableTemplate;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Component;

new class extends Component
{
	/** @var SystemTableTemplate $systemTableModel */
	#[Modelable]
	public string $systemTableModel = '';

	#[Computed]
	public function systemItems(): Collection
	{
		return $this->systemTableModel::all();
	}

	public function newEntry()
	{
		$newEntry = new ($this->systemTableModel)();
		$newEntry->name = __('crud.new_entry');
		$newEntry->order = $this->systemItems()->count();
		$newEntry->className = $this->systemTableModel;
		$newEntry->save();
		unset($this->systemItems);
	}

	public function sort($asc = true)
	{
		$newItems = $this->systemItems()->sortBy('name', descending: !$asc);
		$pos = 1;
		foreach ($newItems as $item)
		{
			$item->order = $pos++;
			$item->save();
		}
	}

	public function updateCrudOrder($id, $position)
	{
		$model = $this->systemTableModel::find($id);
		$position++;
		if ($position == $model->order)
			return;
		$oldPos = $model->order;
		$model->order = $position;
		$model->save();
		if ($oldPos > $position)
		{
			SystemTable::where('className', $this->systemTableModel)
			           ->whereNot('id', $id)
			           ->where('order', '<=', $oldPos)
			           ->where('order', '>=', $position)
			           ->increment('order');
		}
		else
		{
			SystemTable::where('className', $this->systemTableModel)
			           ->whereNot('id', $id)
			           ->where('order', '<=', $position)
			           ->where('order', '>=', $oldPos)
			           ->decrement('order');
		}
		unset($this->systemItems);
	}

	public function updateName($id, $value)
	{
		$model = $this->systemTableModel::find($id);
		$model->name = $value;
		$model->save();
		unset($this->systemItems);
	}

	public function deleteEntry($id)
	{
		$entry = $this->systemTableModel::find($id);
		if ($entry && $entry->canDelete())
			$entry->delete();
		unset($this->systemItems);
	}
};
?>

<div>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">
                <i class="fa-solid fa-table me-2"></i>{{ __('crud.now_editing') }} {{ $systemTableModel::getCrudModelName() }}
            </h5>
            <div class="d-flex align-items-center gap-3">
                <div class="text-muted small d-flex align-items-center gap-1">
                    {{ __('crud.autosort') }}
                    <button class="btn btn-light btn-sm shadow-sm" wire:click="sort" title="Sort A-Z"><i
                                class="fa-solid fa-arrow-up-a-z text-primary"></i></button>
                    <button class="btn btn-light btn-sm shadow-sm" wire:click="sort(false)" title="Sort Z-A"><i
                                class="fa-solid fa-arrow-down-z-a text-primary"></i></button>
                </div>
                <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-2"
                        wire:click="newEntry">
                    <i class="fa-solid fa-plus"></i> {{ __('crud.add') }}
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <ul wire:sort="updateOrder" class="list-group list-group-flush">
                @foreach ($this->systemItems as $item)
                    <li wire:key="{{ $item->id }}" wire:sort:item="{{ $item->id }}"
                        class="list-group-item border-bottom px-4 py-3"
                        x-data="{ isEditing: false }"
                    >
                        <div class="d-flex justify-content-between align-items-center">
                            <div wire:sort:handle class="text-muted me-3" style="cursor: grab;">
                                <i class="fa-solid fa-grip-vertical"></i>
                            </div>
                            <div @click="isEditing = !isEditing; $nextTick(() => $refs.nameInput.focus())"
                                 x-show="!isEditing"
                                 x-cloak
                                 class="flex-grow-1 fw-medium text-dark cursor-pointer py-1" style="cursor: pointer;">
                                {{ $item->name }}
                            </div>
                            <input type="text" x-show="isEditing" x-cloak
                                   wire:change="updateName({{ $item->id }}, $event.target.value)"
                                   @blur="isEditing = false"
                                   class="form-control form-control-sm flex-grow-1 me-3 shadow-none border-primary"
                                   value="{{ $item->name }}"
                                   x-ref="nameInput"
                                   @focus="$el.select()"
                            />
                            <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    @if(!$item->canDelete())
                                        disabled
                                    @else
                                        wire:confirm="{{ __('crud.remove.confirm') }}"
                                    wire:click="deleteEntry({{ $item->id }})"
                                    @endif
                            ><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    </li>
                @endforeach
                @if($this->systemItems->isEmpty())
                    <li class="list-group-item text-center text-muted py-5">
                        <i class="fa-solid fa-inbox fa-3x mb-3 text-light"></i>
                        <p class="mb-0">No entries found.</p>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>