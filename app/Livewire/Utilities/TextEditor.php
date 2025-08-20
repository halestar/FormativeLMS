<?php

namespace App\Livewire\Utilities;

use App\Classes\Storage\DocumentFile;
use App\Classes\Storage\Work\WorkStorage;
use App\Interfaces\Fileable;
use App\Models\Utilities\WorkFile;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class TextEditor extends Component
{
	#[Modelable]
	public string $content = '';
	public WorkStorage $workStorage;
	public Fileable $fileable;
	public array|null $availableTokens = null;
	
	public function mount(WorkStorage $workStorage, Fileable $fileable)
	{
		$this->workStorage = $workStorage;
		$this->fileable = $fileable;
	}
	
	#[On('document-storage-browser.files-selected')]
	public function filesSelected($selected_items)
	{
		$documentFile = DocumentFile::hydrate($selected_items);
		//store the file
		$workFile = $this->workStorage->persistFile($this->fileable, $documentFile);
		$this->dispatch('text-editor.insert-image', work_file: $workFile);
	}
	
	public function removeImages(array $urls)
	{
		Log::info("Removing images");
		foreach($urls as $url)
			$this->removeImage($url);
	}
	
	public function removeImage(string $url)
	{
		Log::info("Removing image: " . $url);
		//first we find the file via the URL
		$workFile = WorkFile::where('url', $url)
		                    ->first();
		if($workFile)
			$workFile->delete();
	}
	
	public function render()
	{
		return view('livewire.utilities.text-editor');
	}
}
