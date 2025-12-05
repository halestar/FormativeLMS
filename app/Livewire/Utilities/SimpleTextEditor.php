<?php

namespace App\Livewire\Utilities;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class SimpleTextEditor extends Component
{
	public int $rows = 5;
    public string $height = "500px";
    public string $width = "100%";
    public string $classes = "";
    public string $style = "";
    public ?string $name = null;
    public string $id;
	#[Modelable]
	public string $content = "";
	
	public function mount(string $instance, string $name = null)
	{
        $this->id = $instance;
        $this->name = $this->name?? $this->id;
	}
	
    public function render()
    {
        return view('livewire.utilities.simple-text-editor');
    }
}
