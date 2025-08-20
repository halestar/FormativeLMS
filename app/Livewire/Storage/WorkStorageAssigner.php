<?php

namespace App\Livewire\Storage;

use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\LmsStorage;
use App\Classes\Storage\Work\WorkStorage;
use Livewire\Attributes\On;
use Livewire\Component;

class WorkStorageAssigner extends Component
{
	public string $title;
	public string $updatedProperty;
	public StorageSettings $storageSettings;
	public null|string|WorkStorage $workStorage = null;
	public bool $saved = true;
	
	public function mount(string $title, string $updatedProperty, StorageSettings $storageSettings)
	{
		$this->title = $title;
		$this->updatedProperty = $updatedProperty;
		$this->storageSettings = $storageSettings;
		$this->workStorage = $this->storageSettings->$updatedProperty;
	}
	
	public function setWorkStorage(string $className)
	{
		$this->workStorage = $className;
		$this->saved = false;
	}
	
	#[On('lms-storage-instance.new-lms-storage')]
	#[On('lms-storage-instance.update-lms-storage')]
	public function createWorkStorage(array $lmsStorage)
	{
		$storage = LmsStorage::hydrate($lmsStorage);
		if(!$storage instanceof WorkStorage)
			return;
		$storage = WorkStorage::hydrate($lmsStorage);
		//are we creating here?
		if(is_string($this->workStorage)) {
			//check that the string is the same as the class name
			if($this->workStorage == get_class($storage)) {
				$this->workStorage = $storage;
				$this->saved = false;
			}
		}
		elseif($this->workStorage instanceof WorkStorage && $this->workStorage->instanceProperty == $storage->instanceProperty) {
			$this->workStorage = $storage;
			$this->saved = false;
		}
	}
	
	#[On('lms-storage-instance.remove-lms-storage')]
	#[On('lms-storage-instance.remove-uninstantiated-lms-storage')]
	public function removeDocumentStorage(string $instanceProperty)
	{
		if(($this->workStorage instanceof WorkStorage && $this->workStorage->instanceProperty == $instanceProperty) ||
			(is_string($this->workStorage) && ($this->workStorage == $instanceProperty))) {
			$this->workStorage = null;
			$this->saved = false;
		}
	}
	
	public function save()
	{
		$updatedProperty = $this->updatedProperty;
		$this->storageSettings->$updatedProperty = $this->workStorage;
		$this->storageSettings->save();
		$this->saved = true;
	}
	
	public function revert()
	{
		$updatedProperty = $this->updatedProperty;
		$this->workStorage = $this->storageSettings->$updatedProperty;
		if($this->workStorage instanceof WorkStorage)
			$this->dispatch('lms-storage-instance.load-lms-storage-' . $this->workStorage->instanceProperty,
				lmsStorage: $this->workStorage->toArray());
		$this->saved = true;
	}
	
	public function render()
	{
		return view('livewire.storage.work-storage-assigner');
	}
}
