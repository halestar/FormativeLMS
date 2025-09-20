<?php

namespace App\Livewire;

use App\Models\People\Phone;
use Illuminate\Support\Collection;
use Livewire\Component;

class PhoneEditor extends Component
{
	public mixed $phoneable;
	public Collection $phones;
	public ?Phone $editing = null;
	public ?Phone $primaryPhone = null;
	public bool $adding = false;
	public string $phone = "";
	public ?string $ext = "";
	public bool $mobile = false;
	public bool $primary = false;
	public string $label = "";
	public ?Phone $linking = null;
	public bool $singlePhoneable;
	
	public function mount(mixed $phoneable): void
	{
		$this->phoneable = $phoneable;
		$this->singlePhoneable = $this->phoneable->isSinglePhoneable();
		$this->phones = $this->singlePhoneable ? collect($this->phoneable->phone) : $this->phoneable->phones;
		if($this->phones->count() == 0)
			$this->adding = true;
		$this->determinePrimary();
	}
	
	private function determinePrimary()
	{
		if($this->singlePhoneable)
			$this->primaryPhone = $this->phoneable->phone;
		else
			$this->primaryPhone = $this->phoneable->phones()
			                                      ->wherePivot('primary', true)
			                                      ->first();
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
		if($this->singlePhoneable)
		{
			$this->phoneable->phone()
			                ->associate($newPhone->id);
			$this->phoneable->save();
		}
		else
		{
			$order = $this->phoneable->phones()
			                         ->count();
			$this->phoneable->phones()
			                ->attach($newPhone,
				                [
					                'primary' => $this->primary,
					                'label' => $this->label,
					                'order' => $order,
				                ]);
			if($this->primary)
				$this->phoneable->makePhonePrimary($newPhone);
		}
		$this->clearForm();
	}
	
	public function clearForm()
	{
		$this->linking = null;
		$this->editing = null;
		$this->adding = false;
		$this->phone = "";
		$this->ext = "";
		$this->primary = false;
		$this->label = "";
		$this->mobile = false;
		$this->phones = $this->singlePhoneable ? collect($this->phoneable->phone) : $this->phoneable->phones;
		if($this->phones->count() == 0)
			$this->adding = true;
		$this->determinePrimary();
	}
	
	public function editPhone(Phone $phone)
	{
		$this->editing = $this->phoneable->phones()
		                                 ->find($phone->id);
		$this->phone = $phone->phone;
		$this->ext = $phone->ext;
		$this->mobile = $phone->mobile;
		$this->primary = $this->editing->personal->primary;
		$this->label = $this->editing->personal->label;
	}
	
	public function updatePhone()
	{
		$this->editing->phone = $this->phone;
		$this->editing->ext = $this->ext;
		$this->editing->mobile = $this->mobile;
		$this->editing->save();
		if(!$this->singlePhoneable)
		{
			$this->phoneable->phones()
			                ->updateExistingPivot($this->editing->id,
				                ['primary' => $this->primary, 'label' => $this->label]);
			if($this->primary)
				$this->phoneable->makePhonePrimary($this->editing);
		}
		$this->clearForm();
	}
	
	public function removePhone(Phone $phone)
	{
		if($this->singlePhoneable)
		{
			$this->phoneable->phone()
			                ->dissociate();
			$this->phoneable->save();
		}
		else
			$this->phoneable->phones()
			                ->detach($phone);
		if($phone->canDelete())
			$phone->delete();
		$this->clearForm();
	}
	
	public function setLinking(Phone $phone)
	{
		$this->clearForm();
		$this->adding = false;
		$this->linking = $phone;
	}
	
	public function linkPhone()
	{
		if($this->linking)
		{
			if($this->singlePhoneable)
			{
				$this->phoneable->phone()
				                ->associate($this->linking);
				$this->phoneable->save();
			}
			else
			{
				$order = $this->phoneable->phones()
				                         ->count();
				$this->phoneable->phones()
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
	
	public function updatePhoneOrder($models)
	{
		foreach($models as $model)
		{
			$this->phoneable->phones()
			                ->updateExistingPivot($model['value'], ['order' => $model['order']]);
		}
		$this->phones = $this->phoneable->phones;
	}
	
	public function render()
	{
		$suggestedPhones = null;
		if(strlen($this->phone) > 2)
			$suggestedPhones = $this->suggestPhone();
		return view('livewire.phone-editor', ['suggestedPhones' => $suggestedPhones]);
	}
	
	public function suggestPhone()
	{
		$query = Phone::where('phone', 'LIKE', '%' . $this->phone . '%');
		if($this->editing)
			$query->whereNot('id', $this->editing->id);
		
		return $query->limit(10)
		             ->get();
	}
}
