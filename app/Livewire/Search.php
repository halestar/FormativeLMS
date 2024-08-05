<?php

namespace App\Livewire;

use App\Models\People\Person;
use Illuminate\Support\Collection;
use Livewire\Component;

class Search extends Component
{
    public string $searchTerm = "";

    public function search()
    {
        $this->results = Person::where('first', 'like', '%' . $this->search . '%')
            ->orWhere('middle', 'like', '%' . $this->search . '%')
            ->orWhere('last', 'like', '%' . $this->search . '%')
            ->orWhere('nick', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->get();
    }

    public function render()
    {
        if(strlen($this->searchTerm) >= 3)
        {
            $results = Person::where('first', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('middle', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('last', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('nick', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('email', 'like', '%' . $this->searchTerm . '%')
                ->get();
        }
        else
        {
            $results = new Collection();
        }
        return view('livewire.search', ['results' => $results]);
    }
}
