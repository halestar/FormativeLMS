<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class CrudItemUpdate extends Component
{
	public Collection $crudItems;
	public string $crudModel;
	
	public function mount(string $model)
	{
		$this->crudModel = $model;
		$this->crudItems = $model::all();
	}
	
	public function updateCrudOrder($models)
	{
		foreach($models as $model)
		{
			$item = ($this->crudModel)::find($model['value']);
			$item->order = $model['order'];
			$item->save();
		}
		$this->crudItems = ($this->crudModel)::all();
	}
	
	public function updateName($id, $value)
	{
		$model = $this->crudModel::find($id);
		$model->name = $value;
		$model->save();
		$this->crudItems = ($this->crudModel)::all();
	}
	
	#[On('change-crud-model')]
	public function changeCrudModel(string $model)
	{
		$this->crudModel = $model;
		$this->crudItems = $model::all();
	}
	
	public function sort($asc = true)
	{
		if($asc)
			$this->crudItems = $this->crudItems->sortBy('name');
		else
			$this->crudItems = $this->crudItems->sortByDesc('name');
		$pos = 1;
		foreach($this->crudItems as $crudItem)
		{
			$crudItem->order = $pos;
			$crudItem->save();
			$pos++;
		}
	}
	
	public function newEntry()
	{
		$newEntry = new ($this->crudModel)();
		$newEntry->name = __('crud.new_entry');
		$newEntry->order = count($this->crudItems);
		$newEntry->className = $this->crudModel;
		$newEntry->save();
		$this->crudItems = $this->crudModel::all();
	}
	
	public function deleteEntry($id)
	{
		$entry = ($this->crudModel)::findOrFail($id);
		$entry->delete();
		$this->crudItems = $this->crudModel::all();
	}
	
	public function render()
	{
		return view('livewire.crud-item-update');
	}
}
