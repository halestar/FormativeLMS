<?php

namespace App\Livewire\Storage;

use App\Classes\Integrators\IntegrationsManager;
use App\Classes\Storage\DocumentFile;
use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\Connections\DocumentFilesConnection;
use App\Models\Integrations\IntegrationConnection;
use App\Models\Integrations\IntegrationService;
use App\Models\People\Person;
use App\Models\Utilities\MimeType;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\File;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentStorageBrowser extends Component
{
	use WithFileUploads;
	
	public bool $init = false;
	public ?Person $user = null;
	public Collection $connections;
	public bool $open = false;
	public bool $multiple = false;
	public bool $allowUpload = false;
	public array $mimeTypes = [];
	public bool $canSelectFolders = false;
	public array $tabs = [];
	public string $selectedConnection = "";
	public $uploadedFiles;
	public string $cb_instance;
	public string $browserKey;

	public null|DocumentFile|array $selectedItems = null;
	
	#[On('document-storage-browser.open-browser')]
	public function open(array $config = null)
	{
		if(!isset($config['cb_instance']) || $config['cb_instance'] == null || $config['cb_instance'] == '')
		{
			$this->failOpen('cb_instance is not set');
			return;
		}
		$this->cb_instance = $config['cb_instance'];
		$this->multiple = $config['multiple'] ?? false;
		$this->mimeTypes = (isset($config['mimetypes']) && is_array($config['mimetypes'])) ?
			array_intersect($config['mimetypes'], MimeType::allowedMimeTypes()) : MimeType::allowedMimeTypes();
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
	
	private function failOpen(string $error): void
	{
		$this->js("console.error('Failed to open document storage browser. ", $error . "');");
	}
	
	private function init()
	{
		$this->user = auth()->user();
        $intManager = app(IntegrationsManager::class);
		$connections = $intManager->personalConnections($this->user, IntegratorServiceTypes::DOCUMENTS);
        $this->connections = collect();
		$this->tabs = [];
		foreach($connections as $connection)
        {
            $this->connections[$connection->id] = $connection;
            $this->tabs[$connection->id] = $connection->service->name;
        }
		if($this->allowUpload)
			$this->tabs['file-upload'] = __('storage.documents.file.upload');
		$array = array_keys($this->tabs);
		$this->selectedConnection = reset($array);
		$this->init = true;
	}
	
	public function setTab(string $connection_id)
	{
		$this->selectedConnection = $connection_id;
		if($this->multiple)
			$this->selectedItems = [];
		else
			$this->selectedItems = null;
        $this->browserKey = uniqid();
		$this->dispatch('document-file-browser.file-selected', selected_items: $this->selectedItems);
		
	}
	
	#[On('document-file-browser.file-selected')]
	public function fileSelected($selected_items)
	{
		if($selected_items == null)
			$this->selectedItems = null;
		elseif(!is_array($selected_items))
			$this->selectedItems = null;
		else
		{
			if($this->multiple)
			{
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
		$this->validate();
		$this->selectedItems = [];
		if(is_array($this->uploadedFiles))
		{
			if(!$this->multiple)
			{
				$this->dispatch('document-storage-browser.error', message: __('errors.storage.files.multiple'));
				return;
			}
			//first, since we're uploading multiple files, we will need to go through each one.
			foreach($this->uploadedFiles as $file)
			{
				//make sure our mime type allows it
				if(count($this->mimeTypes) > 0 && !in_array($file->getMimeType(), $this->mimeTypes))
					continue;
				$this->selectedItems[] = DocumentFile::fromUploadedFile($file);
			}
		}
		elseif(count($this->mimeTypes) == 0 || in_array($this->uploadedFiles->getMimeType(), $this->mimeTypes))
			$this->selectedItems[] = DocumentFile::fromUploadedFile($this->uploadedFiles);
		else
		{
			$this->dispatch('document-storage-browser.error', message: __('errors.storage.files.mimetype'));
			return;
		}
		$this->selectFiles();
	}
	
	public function selectFiles()
	{
		$this->open = false;
		$this->js("$('#document-browser-modal').modal('hide')");
		$this->dispatch('document-storage-browser-files-selected', selected_items: $this->selectedItems,
			cb_instance: $this->cb_instance);
	}
	
	public function render()
	{
		return view('livewire.storage.document-storage-browser');
	}
	
	protected function rules()
	{
		return [
			'uploadedFiles' => File::types(MimeType::allowedMimeTypes())
			                       ->max(12 * 1024),
		];
	}
}
