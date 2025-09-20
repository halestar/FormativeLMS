<?php

namespace App\Livewire;

use App\Models\People\Address;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AddressEditor extends Component
{
	public mixed $addressable;
	public Collection $addresses;
	public ?Address $editing = null;
	public bool $adding = false;
	public ?Address $primaryAddress = null;
	#[Validate('required|max:255')]
	public ?string $line1 = "";
	public ?string $line2 = "";
	public ?string $line3 = "";
	public ?string $city = "";
	public ?string $state = "";
	public ?string $zip = "";
	public ?string $country = "";
	public bool $primary = false;
	public ?string $label = null;
	public ?Address $linking = null;
	public bool $singleAddressable;
	
	public function mount(mixed $addressable): void
	{
		$this->addressable = $addressable;
		$this->singleAddressable = $this->addressable->isSingleAddressable();
		$this->addresses = $this->singleAddressable ? collect($this->addressable->address) : $this->addressable->addresses;
		$this->country = config('lms.default_country');
		if($this->addresses->count() == 0)
			$this->adding = true;
		$this->determinePrimary();
	}
	
	private function determinePrimary()
	{
		if($this->singleAddressable)
			$this->primaryAddress = $this->addressable->address;
		else
			$this->primaryAddress = $this->addressable->addresses()
			                                          ->wherePivot('primary', true)
			                                          ->first();
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
		if($this->singleAddressable)
		{
			$this->addressable->address()
			                  ->associate($newAddress->id);
			$this->addressable->save();
		}
		else
		{
			$order = $this->addressable->addresses()
			                           ->count();
			$this->addressable->addresses()
			                  ->attach($newAddress,
				                  [
					                  'primary' => $this->primary,
					                  'label' => $this->label,
					                  'order' => $order,
				                  ]);
			if($this->primary)
				$this->addressable->makeAddressPrimary($newAddress);
		}
		
		$this->clearForm();
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
		$this->label = null;
		$this->addresses = $this->singleAddressable ? collect($this->addressable->address) : $this->addressable->addresses;
		if($this->addresses->count() == 0)
			$this->adding = true;
		$this->determinePrimary();
	}
	
	public function editAddress(Address $address)
	{
		$this->editing = $this->singleAddressable ? $this->addressable->address : $this->addressable->addresses()
		                                                                                            ->find($address->id);
		$this->line1 = $address->line1;
		$this->line2 = $address->line2;
		$this->line3 = $address->line3;
		$this->city = $address->city;
		$this->state = $address->state;
		$this->zip = $address->zip;
		$this->country = $address->country;
		if(!$this->singleAddressable)
		{
			$this->primary = $this->editing->personal->primary;
			$this->label = $this->editing->personal->label;
		}
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
		if(!$this->singleAddressable)
		{
			$this->addressable->addresses()
			                  ->updateExistingPivot($this->editing->id,
				                  ['primary' => $this->primary, 'label' => $this->label]);
			if($this->primary)
				$this->addressable->makeAddressPrimary($this->editing);
		}
		$this->clearForm();
	}
	
	public function removeAddress(Address $address)
	{
		if($this->singleAddressable)
		{
			$this->addressable->address()
			                  ->dissociate();
			$this->addressable->save();
		}
		else
			$this->addressable->addresses()
			                  ->detach($address);
		if($address->canDelete())
			$address->delete();
		$this->clearForm();
	}
	
	public function setLinking(Address $address)
	{
		$this->clearForm();
		$this->adding = false;
		$this->linking = $address;
	}
	
	public function linkAddress()
	{
		if($this->linking)
		{
			if($this->singleAddressable)
			{
				$this->addressable->address()
				                  ->associate($this->linking);
				$this->addressable->save();
			}
			else
			{
				$order = $this->phoneable->phones()
				                         ->count();
				$this->addressable->addresses()
				                  ->attach($this->linking,
					                  [
						                  'primary' => $this->primary,
						                  'label' => $this->label,
						                  'order' => $order,
					                  ]);
			}
		}
		$this->clearForm();
	}
	
	public function updateAddressOrder($models)
	{
		if(!$this->singleAddressable)
		{
			foreach($models as $model)
			{
				$this->addressable->addresses()
				                  ->updateExistingPivot($model['value'], ['order' => $model['order']]);
			}
			$this->addresses = $this->addressable->addresses;
			$this->determinePrimary();
		}
	}
	
	public function render()
	{
		$suggestedAddresses = null;
		if(strlen($this->line1) > 2)
			$suggestedAddresses = $this->suggestAddresses();
		return view('livewire.address-editor', ['suggestedAddresses' => $suggestedAddresses]);
	}
	
	public function suggestAddresses()
	{
		$query = Address::where(function($query)
		{
			$query->where('line1', 'LIKE', '%' . $this->line1 . '%')
			      ->orWhere('line2', 'LIKE', '%' . $this->line1 . '%')
			      ->orWhere('line3', 'LIKE', '%' . $this->line1 . '%');
		});
		
		if($this->editing)
			$query->whereNot('id', $this->editing->id);
		return $query->limit(10)
		             ->get();
	}
}
