<?php

namespace App\Livewire\People;

use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class PersonSelector extends Component
{
    public ?string $search = null;
    public int $maxResults = 5;
    public int $resultCount = 0;
    public string $selectedCB;
    public string $clearedCB;
    public Collection $people;
    public ?Person $selectedPerson = null;
    public Collection $filterRoles;
    public string $placeholder;
    public string $containerClasses = "w-100";

    public function mount(?string $selectedCB = null, ?string $clearedCB = null, $filterRoles = null, int $maxResults = 5, ?string $placeholder = null, ?string $containerClasses = null)
    {
        $this->selectedCB = $selectedCB?? "console.log(person)";
        $this->clearedCB = $clearedCB?? "console.log('Cleared Person')";
        $this->filterRoles = new Collection();
        if($filterRoles)
        {
            if(is_array($filterRoles))
                $this->filterRoles = new Collection($filterRoles);
            elseif($filterRoles instanceof Collection)
                $this->filterRoles = $filterRoles;
            elseif($filterRoles instanceof SchoolRoles)
                $this->filterRoles = new Collection([$filterRoles]);
        }
        $this->maxResults = $maxResults;
        $this->people = new Collection();
        $this->placeholder = $placeholder ?? __('people.search_for_person');
        $this->containerClasses = $containerClasses ?? "w-100";
    }

    public function selectPerson(int $personId)
    {
        $this->selectedPerson = $this->people->where('id', $personId)->first();
        if($this->selectedPerson)
        {
            $this->search = $this->selectedPerson->name;
            $this->dispatch('person-selected', person: $this->selectedPerson);
        }
    }

    #[On('person-selector-clear-search')]
    public function clearSearch()
    {
        $this->search = "";
        $this->people = new Collection();
        $this->resultCount = 0;
        $this->selectedPerson = null;
        $this->dispatch('person-cleared');
    }

    public function render()
    {
        if($this->search && strlen($this->search) > 2)
        {
            $people = Person::search($this->search);
            if($this->filterRoles->count() > 0)
            {
                $people = $people->query(function($query)
                {
                    return $query->role($this->filterRoles);
                });
            }
            $this->people = $people->take($this->maxResults)->get();
            $this->resultCount = $this->people->count();
        }
        else
        {
            $this->resultCount = 0;
            $this->people = new Collection();
        }
        return view('livewire.people.person-selector');
    }
}
