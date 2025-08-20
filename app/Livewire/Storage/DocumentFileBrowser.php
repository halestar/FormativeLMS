<?php

namespace App\Livewire\Storage;

use App\Classes\Storage\Document\DocumentStorage;
use App\Classes\Storage\DocumentFile;
use App\Models\People\Person;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentFileBrowser extends Component
{
	use WithFileUploads;
	
	public DocumentStorage $documentStorage;
	public Person $person;
	public array $assets;
	public string $filterTerms = '';
	public ?DocumentFile $selectedFolder = null;
	public ?DocumentFile $viewingFile = null;
	public null|DocumentFile|array $selectedItems = null;
	public bool $multiple = false;
	public bool $canSelectFolders = false;
	public array $mimeTypes = [];
	public $uploads;
	
	public function mount(Person $person, DocumentStorage $documentStorage)
	{
		$this->documentStorage = $documentStorage;
		$this->person = $person;
		$this->assets = $this->documentStorage->rootFiles($this->person);
	}
	
	public function clearFiter()
	{
		$this->filterTerms = "";
	}
	
	function viewFolder(string $path)
	{
		$folder = $this->documentStorage->file($this->person, $path);
		if($folder->isFolder) {
			$this->selectedFolder = $folder;
		}
	}
	
	public function viewParent()
	{
		if($this->selectedFolder) {
			$this->selectedFolder = $this->documentStorage->parentDirectory($this->person, $this->selectedFolder);
		}
		
	}
	
	public function viewFile(string $path)
	{
		$file = $this->documentStorage->file($this->person, $path);
		if(!$file->isFolder && $file->canPreview)
			$this->viewingFile = $file;
	}
	
	public function closeView()
	{
		$this->viewingFile = null;
	}
	
	public function removeFile(string $path)
	{
		$file = $this->documentStorage->file($this->person, $path);
		$this->documentStorage->deleteFile($this->person, $file);
	}
	
	public function updateName(string $path, string $newName)
	{
		$file = $this->documentStorage->file($this->person, $path);
		$this->documentStorage->changeName($this->person, $file, $newName);
	}
	
	public function addFolder()
	{
		$defaultName = __('storage.document.folder.default');
		$this->documentStorage->persistFolder($this->person, $defaultName, $this->selectedFolder);
	}
	
	public function moveToFolder(string $path, string $folderPath = '')
	{
		$file = $this->documentStorage->file($this->person, $path);
		if(!$folderPath || $folderPath == '')
			$targetFolder = null;
		else
			$targetFolder = $this->documentStorage->file($this->person, $folderPath);
		$this->documentStorage->changeParent($this->person, $file, $targetFolder);
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
		$file = $this->documentStorage->file($this->person, $path);
		if($this->multiple) {
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
		else {
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
		//we need the manager to convert the images.
		if(is_array($this->uploads)) {
			//first, since we're uploading multiple files, we will need to go through each one.
			foreach($this->uploads as $file) {
				//make sure our mime type allows it
				if(count($this->mimeTypes) > 0 && !in_array($file->getMimeType(), $this->mimeTypes))
					continue;
				$this->documentStorage->persistFile($this->person, $file, $this->selectedFolder);
			}
		}
		elseif(count($this->mimeTypes) == 0 || in_array($this->uploads->getMimeType(), $this->mimeTypes))
			$this->documentStorage->persistFile($this->person, $this->uploads, $this->selectedFolder);
		
		$this->refreshAssets();
	}
	
	public function refreshAssets()
	{
		if($this->selectedFolder)
			$this->assets = $this->documentStorage->files($this->person, $this->selectedFolder, $this->mimeTypes);
		else
			$this->assets = $this->documentStorage->rootFiles($this->person, $this->mimeTypes);
		if($this->filterTerms)
			$this->assets = array_filter($this->assets,
				fn($asset) => Str::contains($asset->name, $this->filterTerms, true));
	}
	
	public function render()
	{
		$this->refreshAssets();
		return view('livewire.storage.document-file-browser');
	}
}
