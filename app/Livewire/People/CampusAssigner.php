<?php

namespace App\Livewire\People;

use App\Interfaces\HasCampuses;
use App\Models\Locations\Campus;
use Livewire\Component;

class CampusAssigner extends Component
{
    public HasCampuses $person;

    public bool $editing = false;

    public function mount(HasCampuses $person)
    {
        $this->person = $person;
    }

    public function changeCampus(Campus $campus, bool $active): void
    {
        if ($active) {
            $this->person->campuses()->attach($campus);
        } else {
            $this->person->campuses()->detach($campus->id);
        }
    }

    public function render()
    {
        return view('livewire.people.campus-assigner');
    }
}
