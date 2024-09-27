<?php

namespace App\Livewire;

use App\Models\People\Person;
use App\Models\People\ViewPolicies\ViewableField;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SelfViewingPermissions extends Component
{
    public Person $person;
    public Collection $unenforcedFields;
    public array $prefs;
    public function mount()
    {
        $this->person = Auth::user();
        $this->unenforcedFields = $this->person->unenforcedFields();
        $this->prefs = $this->person->viewingPreferences();
    }

    public function toggleField(ViewableField $viewableField)
    {
        $this->prefs[$viewableField->id] = !$this->prefs[$viewableField->id];
        $this->person->updateViewingPreferences($this->prefs);
    }

    public function render()
    {
        return view('livewire.self-viewing-permissions');
    }
}
