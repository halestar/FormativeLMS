<?php

namespace App\Livewire;

use App\Models\People\Address;
use App\Models\People\Person;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class AddressEditor extends Component
{
    public Person $person;
    public Collection $addresses;
    public ?Address $editing = null;
    public bool $adding = false;
    public string $line1 = "";
    public string $line2 = "";
    public string $line3 = "";
    public string $city = "";
    public string $state = "";
    public string $zip = "";
    public string $country = "";
    public bool $primary = false;
    public bool $work = false;
    public ?Address $linking = null;

    public function mount(Person $person):void
    {
        $this->person = $person;
        $this->addresses = $person->addresses;
        $this->country = config('lms.default_country');
    }

    public function clearForm()
    {
        $this->linking = null;
        $this->editing = null;
        $this->adding = false;
        $this->line1 = "";
        $this->line2 = "";
        $this->line3 = "";
        $this->city = "";
        $this->state = "";
        $this->zip = "";
        $this->country = config('lms.default_country');
        $this->primary = false;
        $this->work = false;
        $this->addresses = $this->person->addresses;
    }

    public function addAddress()
    {
        $newAddress = new Address();
        $newAddress->fill(
            [
                'line1' => $this->line1,
                'line2' => $this->line2,
                'line3' => $this->line3,
                'city' => $this->city,
                'state' => $this->state,
                'zip' => $this->zip,
                'country' => $this->country,
            ]);
        $newAddress->save();
        if($this->primary)
        {
            //we need to make the other ones not-primary
            DB::update("UPDATE people_addresses SET people_addresses.primary=0 WHERE person_id=?", [$this->person->id]);
        }
        $this->person->addresses()->save($newAddress, ['primary' => $this->primary,'work' => $this->work]);
        $this->clearForm();
    }

    public function editAddress(Address $address)
    {
        $this->editing = $this->person->addresses()->find($address->id);
        $this->line1 = $address->line1;
        $this->line2 = $address->line2;
        $this->line3 = $address->line3;
        $this->city = $address->city;
        $this->state = $address->state;
        $this->zip = $address->zip;
        $this->country = $address->country;
        $this->primary = $this->editing->personal->primary;
        $this->work = $this->editing->personal->work;
    }

    public function updateAddress()
    {
        $this->editing->line1 = $this->line1;
        $this->editing->line2 = $this->line2;
        $this->editing->line3 = $this->line3;
        $this->editing->city = $this->city;
        $this->editing->state = $this->state;
        $this->editing->zip = $this->zip;
        $this->editing->country = $this->country;
        $this->editing->save();
        $this->person->addresses()
            ->updateExistingPivot($this->editing->id, ['primary' => $this->primary,'work' => $this->work]);
        $this->clearForm();
    }

    public function removeAddress(Address $address)
    {
        $this->person->addresses()->detach($address);
        if($address->people()->count() == 0)
            $address->delete();
        $this->clearForm();
    }

    public function suggestAddresses()
    {
         $suggestions = Address::where('line1', 'LIKE',  '%' . $this->line1 . '%')
            ->orWhere('line2', 'LIKE',  '%' . $this->line1 . '%')
            ->orWhere('line3', 'LIKE',  '%' . $this->line1 . '%')
            ->limit(10);
         return $suggestions->get();
    }

    public function setLinking(Address $address)
    {
        $this->clearForm();
        $this->linking = $address;
    }

    public function linkAddress()
    {
        if($this->linking)
            $this->person->addresses()->attach($this->linking, ['primary' => $this->primary,'work' => $this->work]);
        $this->clearForm();
    }

    public function render()
    {
        $suggestedAddresses = null;
        if(strlen($this->line1) > 2)
            $suggestedAddresses = $this->suggestAddresses();
        return view('livewire.address-editor', ['suggestedAddresses' => $suggestedAddresses]);
    }
}
