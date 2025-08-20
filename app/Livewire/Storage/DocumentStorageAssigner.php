<?php

namespace App\Livewire\Storage;

use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\Document\DocumentStorage;
use App\Classes\Storage\LmsStorage;
use Livewire\Attributes\On;
use Livewire\Component;

class DocumentStorageAssigner extends Component
{
	public string $title;
	public string $updatedProperty;
	public StorageSettings $storageSettings;
	public array $documentStorages;
	public bool $saved = true;
	
	public function mount(string $title, string $updatedProperty, StorageSettings $storageSettings)
	{
		$this->title = $title;
		$this->updatedProperty = $updatedProperty;
		$this->storageSettings = $storageSettings;
		$this->documentStorages = $this->storageSettings->$updatedProperty;
	}
	
	public function addDocumentStorage(string $className)
	{
		$this->documentStorages[] = $className;
		$this->saved = false;
	}
	
	#[On('lms-storage-instance.new-lms-storage')]
	public function createDocumentStorage(array $lmsStorage)
	{
		$storage = LmsStorage::hydrate($lmsStorage);
		if(!$storage instanceof DocumentStorage)
			return;
		$storage = DocumentStorage::hydrate($lmsStorage);
		//next, we find the string in the $documentStorages array and replace it with the storage instance
		for($i = 0; $i < count($this->documentStorages); $i++) {
			if(is_string($this->documentStorages[$i]) && $this->documentStorages[$i] == get_class($storage)) {
				//found it!
				$this->documentStorages[$i] = $storage;
				$this->saved = false;
				break;
			}
		}
	}
	
	#[On('lms-storage-instance.update-lms-storage')]
	public function updateDocumentStorage(array $lmsStorage)
	{
		$storage = LmsStorage::hydrate($lmsStorage);
		if(!$storage instanceof DocumentStorage)
			return;
		$storage = DocumentStorage::hydrate($lmsStorage);
		//find the storage instance in the $documentStorages array
		for($i = 0; $i < count($this->documentStorages); $i++) {
			if($this->documentStorages[$i] instanceof DocumentStorage && $this->documentStorages[$i]->instanceProperty == $storage->instanceProperty) {
				//found it
				$this->documentStorages[$i] = $storage;
				$this->saved = false;
				break;
			}
		}
	}
	
	#[On('lms-storage-instance.remove-lms-storage')]
	public function removeDocumentStorage(string $instanceProperty)
	{
		$documentStorage = [];
		$found = false;
		foreach($this->documentStorages as $storage) {
			if($storage instanceof DocumentStorage && $storage->instanceProperty == $instanceProperty) {
				$found = true;
				continue;
			}
			$documentStorage[] = $storage;
		}
		if($found) {
			$this->documentStorages = $documentStorage;
			$this->saved = false;
		}
	}
	
	#[On('lms-storage-instance.remove-uninstantiated-lms-storage')]
	public function removeUninstantiatedDocumentStorage(string $className)
	{
		$this->documentStorages = array_filter($this->documentStorages, function($documentStorage) use ($className) {
			return ($documentStorage instanceof DocumentStorage) || (is_string($documentStorage) && $documentStorage == $className);
		});
		$this->saved = false;
	}
	
	public function save()
	{
		$updatedProperty = $this->updatedProperty;
		$this->storageSettings->$updatedProperty = $this->documentStorages;
		$this->storageSettings->save();
		$this->saved = true;
	}
	
	public function revert()
	{
		$updatedProperty = $this->updatedProperty;
		$this->documentStorages = $this->storageSettings->$updatedProperty;
		foreach($this->documentStorages as $documentStorage)
			$this->dispatch('lms-storage-instance.load-lms-storage-' . $documentStorage->instanceProperty,
				lmsStorage: $documentStorage->toArray());
		$this->saved = true;
	}
	
	public function render()
	{
		return view('livewire.storage.document-storage-assigner');
	}
}
