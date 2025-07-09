<?php

namespace App\Livewire;

use App\Models\People\Person;
use Illuminate\Support\Collection;
use Livewire\Component;

class RelationshipCreator extends Component
{
    public Person $person;
    public Collection $relations;

    public ?int $to_person_id = null;
    public ?Person $to_person = null;
    public ?int $relationship_id = 1;
    public bool $reciprocal = true;
    public ?int $reciprocal_id = null;

    public bool $adding  = false;
    public ?Person $editing = null;
    public string $relation_search = "";

    public function mount(Person $person)
    {
        $this->person = $person;
        $this->relations = $person->relationships;
    }

    public function suggestPeople()
    {
        return Person::where('first', 'LIKE', '%' . $this->relation_search . '%')
            ->orWhere('last', 'LIKE', '%' . $this->relation_search . '%')
            ->orWhere('nick', 'LIKE', '%' . $this->relation_search . '%')
            ->orWhere('email', 'LIKE', '%' . $this->relation_search . '%')
            ->orWhere('middle', 'LIKE', '%' . $this->relation_search . '%')
            ->limit(10)
            ->get();
    }

    public function linkTarget(Person $person)
    {
        $this->to_person_id = $person->school_id;
        $this->to_person = Person::find($this->to_person_id);
        $this->relation_search = "";
    }

    public function clearToPerson()
    {
        $this->to_person_id = null;
        $this->to_person = null;
    }

    public function clearForm()
    {
        $this->to_person_id = null;
        $this->to_person = null;
        $this->relationship_id = null;
        $this->reciprocal = true;
        $this->reciprocal_id = null;
        $this->adding = false;
        $this->editing = null;
        $this->relation_search = "";
        $this->relations = $this->person->relationships;
    }

    public function setEditing(Person $person)
    {
        $this->editing = $person;
        $relationship = $this->person->relationships()->where('to_person_id', $person->id)->first();
        $reverseRelationship = $person->relationships()->where('to_person_id', $this->person->id)->first();
        if($relationship)
            $this->relationship_id = $relationship->personal->relationship_id;
        if($reverseRelationship)
        {
            $this->reciprocal = true;
            $this->reciprocal_id = $reverseRelationship->personal->relationship_id;
        }
        else
        {
            $this->reciprocal = true;
        }
    }

    public function updateRelationship()
    {
        $this->person
            ->relationships()
            ->updateExistingPivot($this->editing->id, ['relationship_id' => $this->relationship_id]);
        if($this->reciprocal)
            $this->editing
                ->relationships()
                ->updateExistingPivot($this->person->id, ['relationship_id' => $this->reciprocal_id]);

        $this->clearForm();
    }

    public function addRelationship()
    {
        $this->person->relationships()->attach($this->to_person_id, ['relationship_id' => $this->relationship_id]);
        if($this->reciprocal)
            $this->to_person->relationships()->attach($this->person->id, ['relationship_id' => $this->reciprocal_id]);
        $this->clearForm();
    }

    public function deleteRelationship(int $toPerson, bool $reciprocal = false)
    {
        $toPerson = Person::find($toPerson);
        $this->person->relationships()->detach($toPerson->id);
        if($reciprocal)
            $toPerson->relationships()->detach($this->person->id);
        $this->clearForm();
    }
    public function render()
    {
        $suggestedPeople = null;
        if(strlen($this->relation_search) > 2)
            $suggestedPeople = $this->suggestPeople();
        return view('livewire.relationship-creator', ['suggestedPeople' => $suggestedPeople]);
    }
}
