<?php

namespace App\Livewire\Utilities;

use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class ModelSwitch extends Component
{
	public Model $model;
	public string $property;
	public ?string $label = null;
	#[Modelable]
	public bool $state;
	public ?string $classes = null;
	public ?string $confirm = null;
	public string $elId;
	public ?string $onChange = null;
    public bool $disabled = false;
	
	public function mount(Model $model, string $property, ?string $elId = null)
	{
		$this->model = $model;
		$this->property = $property;
		$this->state = $model->$property;
		$this->elId = $elId ?? uniqid();
	}
	
	public function toggle()
	{
		$property = $this->property;
		$this->model->$property = $this->state;
		$this->model->save();
	}
	
	public function render()
	{
		return view('livewire.utilities.model-switch');
	}
}
