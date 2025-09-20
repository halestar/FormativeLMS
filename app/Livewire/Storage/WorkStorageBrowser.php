<?php

namespace App\Livewire\Storage;

use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\DocumentFile;
use App\Enums\WorkStoragesInstances;
use App\Interfaces\Fileable;
use App\Models\Integrations\Connections\WorkFilesConnection;
use App\Models\Utilities\MimeType;
use App\Models\Utilities\WorkFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\File;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class WorkStorageBrowser extends Component
{
	use WithFileUploads;
	
	public string $title;
	public WorkStoragesInstances $storageInstance;
	public StorageSettings $settings;
	public WorkFilesConnection $connection;
	public Fileable $fileable;
	public Collection $workFiles;
	public $uploadedFiles;
	public array $mimeTypes = [];
	
	public function mount(Fileable $fileable, StorageSettings $settings, string $title = null)
	{
		$this->title = $title?? __('storage.work.browser');
		$this->fileable = $fileable;
		$this->storageInstance = $this->fileable->getWorkStorageKey();
		$this->settings = $settings;
		$this->connection = $this->settings->getWorkConnection($this->storageInstance);
		$this->refreshFiles();
	}
	
	private function refreshFiles()
	{
		$this->workFiles = $this->fileable->workFiles()
		                                  ->visible()
		                                  ->get();
	}
	
	protected function rules()
	{
		return [
			'uploadedFiles' => File::types(MimeType::allowedMimeTypes())
			                       ->max(12 * 1024),
		];
	}
	
	public function uploadFiles($fileName)
	{
		$this->validate();
		//we need the manager to convert the images.
		if(is_array($this->uploadedFiles)) {
			//first, since we're uploading multiple files, we will need to go through each one.
			foreach($this->uploadedFiles as $file) {
				//make sure our mime type allows it
				if(count($this->mimeTypes) > 0 && !in_array($file->getMimeType(), $this->mimeTypes))
					continue;
				$docFile = DocumentFile::fromUploadedFile($file);
				$this->connection->persistFile($this->fileable, $docFile);
			}
		}
		elseif(count($this->mimeTypes) == 0 || in_array($this->uploadedFiles->getMimeType(), $this->mimeTypes)) {
			$docFile = DocumentFile::fromUploadedFile($this->uploadedFiles);
			$this->connection->persistFile($this->fileable, $docFile);
		}
		$this->refreshFiles();
	}
	
	public function removeFile(WorkFile $workFile)
	{
		$workFile->delete();
		$this->refreshFiles();
	}
	
	#[On('document-storage-browser.files-selected')]
	public function filesSelected($cb_instance, $selected_items)
	{
		Log::debug('selected items: ' . print_r($selected_items, true));
		if($cb_instance != 'work-storage-browser') return;
		foreach($selected_items as $item) {
			$documentFile = DocumentFile::hydrate($item);
			$this->connection->persistFile($this->fileable, $documentFile);
		}
		$this->refreshFiles();
	}
	
	
	public function render()
	{
		return view('livewire.storage.work-storage-browser');
	}
}
