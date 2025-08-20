<?php

namespace App\Livewire\Storage;

use App\Classes\Storage\LmsStorage;
use Livewire\Attributes\On;
use Livewire\Component;

class LmsStorageInstance extends Component
{
	public ?LmsStorage $lmsStorage = null;
	public string $className = '';
	public bool $editing = false;
	public string $instanceProperty = '';
	public string $displayName = '';
	
	
	public function mount(string|LmsStorage $lStorage)
	{
		// if $lmsStorage is a string, it is a class name, which means we go into edit mode
		if(is_string($lStorage)) {
			$this->lmsStorage = null;
			$this->className = $lStorage;
			$this->editing = true;
			$this->instanceProperty = '';
			$this->displayName = '';
		}
		else {
			$this->lmsStorage = $lStorage;
			$this->className = get_class($lStorage);
			$this->editing = false;
			$this->instanceProperty = $this->lmsStorage->instanceProperty;
			$this->displayName = $this->lmsStorage->displayName;
		}
	}
	
	public function updateLmsStorage()
	{
		$this->validate();
		$className = $this->className;
		$newLmsStorage = (!$this->lmsStorage);
		//are we creating or updating?
		if($newLmsStorage)
			$this->lmsStorage = new ($className)($this->instanceProperty, $this->displayName);
		//update the fields.
		$this->lmsStorage->displayName = $this->displayName;
		//optional fields
		if($newLmsStorage)
			$this->dispatch('lms-storage-instance.new-lms-storage', lmsStorage: $this->lmsStorage->toArray());
		else
			$this->dispatch('lms-storage-instance.update-lms-storage', lmsStorage: $this->lmsStorage->toArray());
		$this->editing = false;
	}
	
	#[On('lms-storage-instance.load-lms-storage-{instanceProperty}')]
	public function loadLmsStorage(array $lmsStorage)
	{
		$this->lmsStorage = LmsStorage::hydrate($lmsStorage);
		$this->className = get_class($this->lmsStorage);
		$this->instanceProperty = $this->lmsStorage->instanceProperty;
		$this->displayName = $this->lmsStorage->displayName;
		$this->editing = false;
	}
	
	public function render()
	{
		return view('livewire.storage.lms-storage-instance');
	}
	
	protected function rules()
	{
		$className = $this->className;
		return ($className)::rules($this->lmsStorage?->instanceProperty) +
			[
				'displayName' => 'required|min:3',
			];
	}
}
