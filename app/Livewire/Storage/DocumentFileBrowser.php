<?php

namespace App\Livewire\Storage;

use App\Classes\Storage\DocumentFile;
use App\Models\Integrations\Connections\DocumentFilesConnection;
use App\Models\Utilities\MimeType;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentFileBrowser extends Component
{
	use WithFileUploads;
	
	public DocumentFilesConnection $connection;
	public array $assets;
	public string $filterTerms = '';
	public ?DocumentFile $selectedFolder = null;
	public ?DocumentFile $viewingFile = null;
	public null|DocumentFile|array $selectedItems = null;
	public bool $multiple = false;
	public bool $canSelectFolders = false;
	public array $mimeTypes;
	public $uploads;
	
	public function mount(DocumentFilesConnection $connection, array $mimeTypes = null)
	{
		$this->connection = $connection;
		$this->assets = $this->connection->rootFiles();
		$this->mimeTypes = $mimeTypes ? array_intersect($mimeTypes,
			MimeType::allowedMimeTypes()) : MimeType::allowedMimeTypes();
	}
	
	public function clearFilter()
	{
		$this->filterTerms = "";
	}
	
	function viewFolder(string $path)
	{
		$folder = $this->connection->file($path);
		if($folder->isFolder)
		{
			$this->selectedFolder = $folder;
		}
	}
	
	public function viewParent()
	{
		if($this->selectedFolder)
		{
			$this->selectedFolder = $this->connection->parentDirectory($this->selectedFolder);
		}
		
	}
	
	public function viewFile(string $path)
	{
		$file = $this->connection->file($path);
		if(!$file->isFolder && $file->canPreview)
			$this->viewingFile = $file;
	}
	
	public function closeView()
	{
		$this->viewingFile = null;
	}
	
	public function removeFile(string $path)
	{
		$file = $this->connection->file($path);
		$this->connection->deleteFile($file);
	}
	
	public function updateName(string $path, string $newName)
	{
		$file = $this->connection->file($path);
		$this->connection->changeName($file, $newName);
	}
	
	public function addFolder()
	{
		$defaultName = __('storage.document.folder.default');
		$this->connection->persistFolder($defaultName, $this->selectedFolder);
	}
	
	public function moveToFolder(string $path, string $folderPath = '')
	{
		$file = $this->connection->file($path);
		if(!$folderPath || $folderPath == '')
			$targetFolder = null;
		else
			$targetFolder = $this->connection->file($folderPath);
		$this->connection->changeParent($file, $targetFolder);
	}
	
	public function isSelected(DocumentFile $file): bool
	{
		if($this->selectedItems === null) return false;
		if($this->selectedItems instanceof DocumentFile)
			return $this->selectedItems->path === $file->path;
		return isset($this->selectedItems[$file->path]);
	}
	
	public function selectFile(string $path)
	{
		$file = $this->connection->file($path);
		if($this->multiple)
		{
			//since multiple is set it should ALWAYS be an array
			if(!$this->selectedItems)
				$this->selectedItems = [];
			elseif($this->selectedItems instanceof DocumentFile)
				$this->selectedItems = [$this->selectedItems->path => $this->selectedItems];
			if(isset($this->selectedItems[$file->path]))
				unset($this->selectedItems[$file->path]);
			else
				$this->selectedItems[$file->path] = $file;
		}
		else
		{
			if(!$this->selectedItems)
				$this->selectedItems = $file;
			elseif($this->selectedItems instanceof DocumentFile && $this->selectedItems->path === $file->path)
				$this->selectedItems = null;
			else
				$this->selectedItems = $file;
		}
		$this->dispatch('document-file-browser.file-selected', selected_items: $this->selectedItems);
	}
	
	public function addFile()
	{
		$this->validate();
		//we need the manager to convert the images.
		if(is_array($this->uploads))
		{
			//first, since we're uploading multiple files, we will need to go through each one.
			foreach($this->uploads as $file)
			{
				//make sure our mime type allows it
				if(count($this->mimeTypes) > 0 && !in_array($file->getMimeType(), $this->mimeTypes))
					continue;
				$this->connection->persistFile($file, $this->selectedFolder);
			}
		}
		elseif(count($this->mimeTypes) == 0 || in_array($this->uploads->getMimeType(), $this->mimeTypes))
			$this->connection->persistFile($this->uploads, $this->selectedFolder);
		
		$this->refreshAssets();
	}
	
	public function refreshAssets()
	{
		if($this->selectedFolder)
			$this->assets = $this->connection->files($this->selectedFolder, $this->mimeTypes);
		else
			$this->assets = $this->connection->rootFiles($this->mimeTypes);
		if($this->filterTerms)
			$this->assets = array_filter($this->assets,
				fn($asset) => Str::contains($asset->name, $this->filterTerms, true));
	}
	
	public function render()
	{
		$this->refreshAssets();
		return view('livewire.storage.document-file-browser');
	}
	
	protected function rules()
	{
		return [
			'uploads' => File::types(MimeType::allowedMimeTypes())
			                 ->max(12 * 1024),
		];
	}
}
