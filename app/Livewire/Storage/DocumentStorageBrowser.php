<?php

namespace App\Livewire\Storage;

use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\DocumentFile;
use App\Models\People\Person;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentStorageBrowser extends Component
{
	use WithFileUploads;
	
	public bool $init = false;
	public Person $user;
	public StorageSettings $storageSettings;
	public array $documentStorages = [];
	public bool $open = false;
	public bool $multiple = false;
	public bool $allowUpload = false;
	public array $mimeTypes = [];
	public bool $canSelectFolders = false;
	public array $tabs = [];
	public string $selectedTab = '';
	public $uploadedFiles;
	public string $cb_instance;
	public string $browserKey;
	
	public null|DocumentFile|array $selectedItems = null;
	
	#[On('document-storage-browser.open-browser')]
	public function open(array $config = null)
	{
		if(!isset($config['cb_instance']) || $config['cb_instance'] == null || $config['cb_instance'] == '') {
			$this->failOpen('cb_instance is not set');
			return;
		}
		$this->cb_instance = $config['cb_instance'];
		$this->multiple = $config['multiple'] ?? false;
		$this->mimeTypes = $config['mimetypes'] ?? [];
		$this->allowUpload = $config['allowUpload'] ?? false;
		$this->canSelectFolders = $config['canSelectFolders'] ?? false;
		if($this->multiple)
			$this->selectedItems = [];
		else
			$this->selectedItems = null;
		if(!$this->init)
			$this->init();
		//generate a browser key
		$this->browserKey = uniqid();
		$this->open = true;
		$this->js("$('#document-browser-modal').modal('show');");
	}
	
	private function init()
	{
		$this->user = auth()->user();
		$this->storageSettings = app()->make(StorageSettings::class);
		//either this is an employee or a student.  At least for now.
		if($this->user->isEmployee())
			$this->documentStorages = $this->storageSettings->employee_documents;
		elseif($this->user->isStudent())
			$this->documentStorages = $this->storageSettings->student_documents;
		//add the tabs
		foreach($this->documentStorages as $storage)
			$this->tabs[$storage->instanceProperty] = $storage::prettyName();
		if($this->allowUpload)
			$this->tabs['upload'] = __('storage.documents.file.upload');
		$array = array_keys($this->tabs);
		$this->selectedTab = reset($array);
		$this->init = true;
	}
	
	public function setTab(string $tab)
	{
		$this->selectedTab = $tab;
		if($this->multiple)
			$this->selectedItems = [];
		else
			$this->selectedItems = null;
		//we also need to
		$this->dispatch('document-file-browser.file-selected', selected_items: $this->selectedItems);
		
	}
	
	#[On('document-file-browser.file-selected')]
	public function fileSelected($selected_items)
	{
		if($selected_items == null)
			$this->selectedItems = null;
		elseif(!is_array($selected_items))
			$this->selectedItems = null;
		else {
			if($this->multiple) {
				$this->selectedItems = [];
				foreach($selected_items as $item)
					$this->selectedItems[] = DocumentFile::hydrate($item);
			}
			else
				$this->selectedItems = DocumentFile::hydrate($selected_items);
		}
	}
	
	public function uploadFiles()
	{
		if(is_array($this->uploadedFiles)) {
			if(!$this->multiple) {
				$this->dispatch('document-storage-browser.error', message: __('errors.storage.files.multiple'));
				return;
			}
			$this->selectedItems = [];
			//first, since we're uploading multiple files, we will need to go through each one.
			foreach($this->uploadedFiles as $file) {
				//make sure our mime type allows it
				if(count($this->mimeTypes) > 0 && !in_array($file->getMimeType(), $this->mimeTypes))
					continue;
				$this->selectedItems[] = DocumentFile::fromUploadedFile($file);
			}
		}
		elseif(count($this->mimeTypes) == 0 || in_array($this->uploads->getMimeType(), $this->mimeTypes))
			$this->selectedItems = DocumentFile::fromUploadedFile($this->uploadedFiles);
		else {
			$this->dispatch('document-storage-browser.error', message: __('errors.storage.files.mimetype'));
			return;
		}
		$this->selectFiles();
	}
	
	public function selectFiles()
	{
		$this->open = false;
		$this->js("$('#document-browser-modal').modal('hide')");
		$this->dispatch('document-storage-browser.files-selected', selected_items: $this->selectedItems,
			cb_instance: $this->cb_instance);
	}
	
	public function render()
	{
		return view('livewire.storage.document-storage-browser');
	}
	
	private function failOpen(string $error): void
	{
		$this->js("console.error('Failed to open document storage browser. ", $error . "');");
	}
}
