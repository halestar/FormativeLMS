<?php

namespace App\Livewire;

use App\Models\People\Address;
use App\Models\People\Person;
use App\Models\People\Phone;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PhoneEditor extends Component
{
    public Person $person;
    public Collection $phones;
    public ?Phone $editing = null;
    public bool $adding = false;
    public string $phone = "";
    public string $ext = "";
    public string $mobile = "";
    public bool $primary = false;
    public bool $work = false;
    public ?Phone $linking = null;

    public function mount(Person $person):void
    {
        $this->person = $person;
        $this->phones = $person->phones;
    }

    public function clearForm()
    {
        $this->linking = null;
        $this->editing = null;
        $this->adding = false;
        $this->phone = "";
        $this->ext = "";
        $this->primary = false;
        $this->work = false;
        $this->mobile = false;
        $this->phones = $this->person->phones;
    }

    public function suggestPhone()
    {
        return Phone::where('phone', 'LIKE',  '%' . $this->phone . '%')
            ->limit(10)->get();
    }

    public function addPhone()
    {
        $newPhone = new Phone();
        $newPhone->fill(
            [
                'phone' => $this->phone,
                'ext' => $this->ext,
                'mobile' => $this->mobile,
            ]);
        $newPhone->save();
        if($this->primary)
        {
            //we need to make the other ones not-primary
            DB::update("UPDATE people_phones SET people_phones.primary=0 WHERE person_id=?", [$this->person->id]);
        }
        $this->person->phones()->save($newPhone, ['primary' => $this->primary,'work' => $this->work]);
        $this->clearForm();
    }

    public function editPhone(Phone $phone)
    {
        $this->editing = $this->person->phones()->find($phone->id);
        $this->phone = $phone->line1;
        $this->ext = $phone->ext;
        $this->mobile = $phone->mobile;
        $this->primary = $this->editing->personal->primary;
        $this->work = $this->editing->personal->work;
    }

    public function updatePhone()
    {
        $this->editing->phone = $this->phone;
        $this->editing->ext = $this->ext;
        $this->editing->mobile = $this->mobile;
        $this->editing->save();
        $this->person->phones()
            ->updateExistingPivot($this->editing->id, ['primary' => $this->primary,'work' => $this->work]);
        $this->clearForm();
    }

    public function removePhone(Phone $phone)
    {
        $this->person->phones()->detach($phone);
        if($phone->people()->count() == 0)
            $phone->delete();
        $this->clearForm();
    }

    public function setLinking(Phone $phone)
    {
        $this->clearForm();
        $this->linking = $phone;
    }

    public function linkPhone()
    {
        if($this->linking)
            $this->person->phones()->attach($this->linking, ['primary' => $this->primary,'work' => $this->work]);
        $this->clearForm();
    }
    public function render()
    {
        $suggestedPhones = null;
        if(strlen($this->phone) > 2)
            $suggestedPhones = $this->suggestPhone();
        return view('livewire.phone-editor', ['suggestedPhones' => $suggestedPhones]);
    }
}
