<?php

namespace App\Livewire;

use App\Models\People\Person;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PersonAdder extends Component
{
    #[Validate('nullable')]
    public string $first = "";
    #[Validate('required', message: 'You must enter a last name to add a new person')]
    public string $last = "";
    #[Validate('nullable|email|unique:people,email', message: 'You must enter a valid email address')]
    public string $email = "";

    public Person $self;

    public function mount()
    {
        $this->self = Auth::user();
    }

    public function suggestPeople()
    {
        $query = Person::whereNull('id');
        if(strlen($this->first) > 2)
            $query->orWhere('first', 'like', '%' . $this->first . '%');
        if(strlen($this->last) > 2)
            $query->orWhere('last', 'like', '%' . $this->last . '%');
        if(strlen($this->email) > 2)
            $query->orWhere('email', 'like', '%' . $this->email . '%');
        return $query->get();
    }

    public function addPerson()
    {
        $newPerson = new Person();
        $newPerson->first = $this->first;
        $newPerson->last = $this->last;
        $newPerson->email = $this->email;
        $newPerson->save();
        return redirect(route('people.show', ['person' => $newPerson->school_id]));
    }

    public function showPerson($person_id)
    {
        return redirect(route('people.show', ['person' => $person_id]));
    }

    public function render()
    {
        $suggestedPeople = null;
        if(strlen($this->first) > 2 || strlen($this->last) > 2 || strlen($this->email) > 2)
            $suggestedPeople = $this->suggestPeople();
        return view('livewire.person-adder', ['suggestedPeople' => $suggestedPeople]);
    }
}
