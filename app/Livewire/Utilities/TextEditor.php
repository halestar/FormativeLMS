<?php

namespace App\Livewire\Utilities;

use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\DocumentFile;
use App\Enums\WorkStoragesInstances;
use App\Interfaces\Fileable;
use App\Models\Integrations\IntegrationConnection;
use App\Models\Utilities\MimeType;
use App\Models\Utilities\WorkFile;
use Illuminate\Validation\Rules\File;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class TextEditor extends Component
{
	use WithFileUploads;
	
	#[Modelable]
	public string $content = '';
	public WorkStoragesInstances $instance;
	public IntegrationConnection $connection;
	public Fileable $fileable;
	public array|null $availableTokens = null;
	public $uploadedFile;
	
	public function mount(Fileable $fileable, StorageSettings $settings)
	{
		$this->fileable = $fileable;
		$this->instance = $fileable->getWorkStorageKey();
		$this->connection = $settings->getWorkConnection($this->instance);
	}
	
	#[On('document-storage-browser.files-selected')]
	public function filesSelected($cb_instance, $selected_items)
	{
		if($cb_instance != 'text-editor') return;
		$documentFile = DocumentFile::hydrate($selected_items);
		//store the file
		$workFile = $this->connection->persistFile($this->fileable, $documentFile, true);
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
		$workFile = $this->connection->persistFile($this->fileable, $docFile, true);
		return $workFile->url;
	}
	
	public function render()
	{
		return view('livewire.utilities.text-editor');
	}
	
	protected function rules()
	{
		return [
			'uploadedFile' => File::types(MimeType::allowedMimeTypes())
			                      ->max(12 * 1024),
		];
	}
}
