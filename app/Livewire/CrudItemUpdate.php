<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Attributes\On;

class CrudItemUpdate extends Component
{
    public Collection $crudItems;
    public string $crudModel;

    public function mount(string $model)
    {
        $this->crudModel = $model;
        $this->crudItems = $model::crudItems();
    }
    public function updateCrudOrder($models)
    {
        foreach ($models as $model)
            $this->crudModel::find($model['value'])->setCrudOrder($model['order']);
        $this->crudItems = $this->crudModel::crudItems();
    }

    public function updateName($id, $value)
    {
        $this->crudModel::find($id)->setCrudName($value);
        $this->crudItems = $this->crudModel::crudItems();
    }

    #[On('change-crud-model')]
    public function changeCrudModel(string $model)
    {
        $this->crudModel = $model;
        $this->crudItems = $model::crudItems();
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
            $crudItem->setCrudOrder($pos);
            $pos++;
        }
    }

    public function newEntry()
    {
        $newEntry = new ($this->crudModel)();
        $newEntry->setCrudName(__('crud.new_entry'));
        $newEntry->setCrudOrder(count($this->crudItems));
        $newEntry->save();
        $this->crudItems = $this->crudModel::crudItems();
    }

    public function deleteEntry($id)
    {
        $entry = ($this->crudModel)::findOrFail($id);
        $entry->delete();
        $this->crudItems = $this->crudModel::crudItems();
    }

    public function render()
    {
        return view('livewire.crud-item-update');
    }
}
