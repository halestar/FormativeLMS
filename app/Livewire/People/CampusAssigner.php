<?php

namespace App\Livewire\People;

use App\Models\Locations\Campus;
use App\Models\People\Person;
use Livewire\Component;

class CampusAssigner extends Component
{
    public Person $person;
    public bool $editing = false;

    public function mount(Person $person)
    {
        $this->person = $person;
    }

    public function changeCampus(Campus $campus, bool $active): void
    {
        if($active)
            $this->person->employeeCampuses()->attach($campus);
        else
            $this->person->employeeCampuses()->detach($campus->id);
    }

    public function render()
    {
        return view('livewire.people.campus-assigner');
    }
}
