<?php

namespace App\Livewire\Storage;

use App\Classes\Storage\DocumentFile;
use App\Classes\Storage\Work\WorkStorage;
use App\Interfaces\Fileable;
use App\Models\Utilities\WorkFile;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class WorkStorageBrowser extends Component
{
	use WithFileUploads;
	
	public string $title;
	public WorkStorage $workStorage;
	public Fileable $fileable;
	public Collection $workFiles;
	public $uploadedFiles;
	public array $mimeTypes = [];
	
	public function mount(WorkStorage $workStorage, Fileable $fileable)
	{
		$this->title = __('storage.work.browser');
		$this->workStorage = $workStorage;
		$this->fileable = $fileable;
		$this->refreshFiles();
	}
	
	private function refreshFiles()
	{
		$this->workFiles = $this->fileable->workFiles()
		                                  ->shown()
		                                  ->get();
	}
	
	public function uploadFiles($fileName)
	{
		//we need the manager to convert the images.
		if(is_array($this->uploadedFiles)) {
			//first, since we're uploading multiple files, we will need to go through each one.
			foreach($this->uploadedFiles as $file) {
				//make sure our mime type allows it
				if(count($this->mimeTypes) > 0 && !in_array($file->getMimeType(), $this->mimeTypes))
					continue;
				$docFile = DocumentFile::fromUploadedFile($file);
				$this->workStorage->persistFile($this->fileable, $docFile);
			}
		}
		elseif(count($this->mimeTypes) == 0 || in_array($this->uploadedFiles->getMimeType(), $this->mimeTypes)) {
			$docFile = DocumentFile::fromUploadedFile($this->uploadedFiles);
			$this->workStorage->persistFile($this->fileable, $docFile);
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
		if($cb_instance != 'work-storage-browser') return;
		foreach($selected_items as $item) {
			$documentFile = DocumentFile::hydrate($item);
			$this->workStorage->persistFile($this->fileable, $documentFile);
		}
		$this->refreshFiles();
	}
	
	
	public function render()
	{
		return view('livewire.storage.work-storage-browser');
	}
}
