<?php

namespace App\Livewire\Utilities;

use App\Classes\Storage\DocumentFile;
use App\Classes\Storage\Work\WorkStorage;
use App\Interfaces\Fileable;
use App\Models\Utilities\WorkFile;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class TextEditor extends Component
{
	use WithFileUploads;
	#[Modelable]
	public string $content = '';
	public WorkStorage $workStorage;
	public Fileable $fileable;
	public array|null $availableTokens = null;
	public $uploadedFile;
	
	public function mount(WorkStorage $workStorage, Fileable $fileable)
	{
		$this->workStorage = $workStorage;
		$this->fileable = $fileable;
	}
	
	#[On('document-storage-browser.files-selected')]
	public function filesSelected($cb_instance, $selected_items)
	{
		if($cb_instance != 'text-editor') return;
		$documentFile = DocumentFile::hydrate($selected_items);
		//store the file
		$workFile = $this->workStorage->persistFile($this->fileable, $documentFile, true);
		$this->dispatch('text-editor.insert-image', work_file: $workFile);
	}
	
	public function removeImages(array $urls)
	{
		foreach($urls as $url)
			$this->removeImage($url);
	}
	
	public function removeImage(string $url)
	{
		//first we find the file via the URL
		$workFile = WorkFile::where('url', $url)
		                    ->first();
		if($workFile)
			$workFile->delete();
	}
	
	public function uploadFile()
	{
		$docFile = DocumentFile::fromUploadedFile($this->uploadedFile);
		//store the file
		$workFile = $this->workStorage->persistFile($this->fileable, $docFile, true);
		return $workFile->url;
	}
	
	public function render()
	{
		return view('livewire.utilities.text-editor');
	}
}
