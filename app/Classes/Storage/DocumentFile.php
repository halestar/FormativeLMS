<?php

namespace App\Classes\Storage;

use App\Classes\Settings\StorageSettings;
use App\Models\People\Person;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Blade;
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
	){}
	
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
		);
	}
	
	public function person(): Person
	{
		return Person::where('school_id', $this->person_id)->first();
	}
	
	public function preview(): string
	{
		$storageSettings = app()->make(StorageSettings::class);
		$storage = $storageSettings->getInstance($this->storageInstance);
		return $storage->previewFile($this->person(), $this);
	}
}