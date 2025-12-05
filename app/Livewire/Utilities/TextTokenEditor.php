<?php

namespace App\Livewire\Utilities;

use App\Classes\Settings\StorageSettings;
use App\Interfaces\Fileable;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class TextTokenEditor extends Component
{
    #[Modelable]
    public string $content = '';
    public string $height = "500px";
    public string $width = "100%";
    public string $classes = "";
    public string $style = "";
    public ?string $name = null;
    public string $id;
    public array $availableTokens = [];

    public function mount(string $instanceId, array $availableTokens)
    {
        $this->id = $instanceId;
        $this->name = $this->name?? $this->id;
        $this->availableTokens = $availableTokens;
    }

    public function render()
    {
        return view('livewire.utilities.text-token-editor');
    }
}
