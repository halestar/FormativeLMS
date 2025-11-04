<?php

namespace App\Livewire\Utilities;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class SimpleTextEditor extends Component
{
	public int $rows = 5;
	public string $classes = 'form-control';
	public string $name;
	public string $instance;
	public string $style = "";
	#[Modelable]
	public string $content = "";
	
	public function mount(string $instance, string $name = null)
	{
		$this->instance = $instance;
		$this->name = $name ?? $instance;
	}
	
    public function render()
    {
        return view('livewire.utilities.simple-text-editor');
    }
}
