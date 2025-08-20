<?php

namespace App\Classes\Storage;

use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\Document\DocumentStorage;
use App\Models\People\Person;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use JsonSerializable;

class DocumentFile implements Arrayable, JsonSerializable
{
	
	
	public function __construct
	(
		public int $person_id,
		public bool $isFolder,
		public string $name,
		public string $storageInstance,
		public string $path,
		public string $icon = '',
		public string $mimeType = '',
		public int $size = 0,
		public bool $canPreview = false,
		public bool $canDelete = true,
		public bool $canChangeName = false,
		public bool $isUpload = false
	){}
	
	public static function fromUploadedFile(UploadedFile $file): DocumentFile
	{
		return new DocumentFile
		(
			0,
			false,
			$file->getClientOriginalName() . "." . $file->getClientOriginalExtension(),
			'',
			$file->path(),
			'',
			$file->getMimeType(),
			$file->getSize(),
			false,
			false,
			false,
			true
		);
	}
	
	public function safeName()
	{
		return preg_replace('/[^a-zA-Z0-9\-_.]/', '', $this->name);
	}
	public function toArray()
	{
		return
		[
			'person_id' => $this->person_id,
			'isFolder' => $this->isFolder,
			'name' => $this->name,
			'path' => $this->path,
			'mimeType' => $this->mimeType,
			'size' => $this->size,
			'storageInstance' => $this->storageInstance,
			'icon' => $this->icon,
			'canPreview' => $this->canPreview,
			'canDelete' => $this->canDelete,
			'canChangeName' => $this->canChangeName,
			'isUpload' => $this->isUpload,
		];
	}
	
	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
	
	public static function hydrate(array $data): DocumentFile
	{
		return new DocumentFile
		(
			$data['person_id'],
			$data['isFolder'],
			$data['name'],
			$data['storageInstance'],
			$data['path'],
			$data['icon'],
			$data['mimeType'],
			$data['size'],
			$data['canPreview'],
			$data['canDelete'],
			$data['canChangeName'],
			$data['isUpload'],
		);
	}
	
	public function person(): Person
	{
		return Person::where('school_id', $this->person_id)->first();
	}
	
	public function storageInstance(): DocumentStorage
	{
		$storageSettings = app()->make(StorageSettings::class);
		return $storageSettings->getInstance($this->storageInstance);
	}
	
	public function preview(): string
	{
		$storageSettings = app()->make(StorageSettings::class);
		$storage = $storageSettings->getInstance($this->storageInstance);
		return $storage->previewFile($this->person(), $this);
	}
	
	public function getExportFile(): ?ExportFile
	{
		if($this->isUpload) {
			$info = pathinfo($this->path);
			return new ExportFile
			(
				$info['filename'],
				File::get($this->path),
				$this->mimeType,
				$info['extension'],
				$this->size
			);
		}
		return $this->storageInstance()
		            ->exportFile($this->person(), $this);
	}
}