<?php

namespace App\Livewire\Locations;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class MassCreatePeriodCreator extends Component
{
    #[Modelable]
    public array $periods;

    public function mount(array $periods)
    {
        $this->periods = $periods;
    }

    public function updateName($id, string $name)
    {
        $this->periods[$id]['name'] = $name;
    }
    public function updateAbbr($id, string $abbr)
    {
        $this->periods[$id]['abbr'] = $abbr;
    }
    public function updateDay($id, int $day)
    {
        $this->periods[$id]['day'] = $day;
    }
    public function updateStart($id, string $start)
    {
        $this->periods[$id]['start'] = $start;
    }
    public function updateEnd($id, string $end)
    {
        $this->periods[$id]['end'] = $end;
    }

    public function updateSelected($id, bool $selected)
    {
        $this->periods[$id]['selected'] = $selected;
    }
    public function render()
    {
        return view('livewire.locations.mass-create-period-creator');
    }
}
