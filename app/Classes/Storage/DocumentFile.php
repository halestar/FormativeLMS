<?php

namespace App\Classes\Storage;

use App\Interfaces\Synthesizable;
use App\Models\Integrations\Connections\DocumentFilesConnection;
use App\Models\Integrations\IntegrationConnection;
use App\Models\People\Person;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class DocumentFile implements Synthesizable
{
	
	
	public function __construct
	(
		public int $school_id,
		public bool $isFolder,
		public string $name,
		public string $connection_id,
		public string $path,
		public string $icon = '',
		public string $mimeType = '',
		public int $size = 0,
		public bool $canPreview = false,
		public bool $canDelete = false,
		public bool $canChangeName = false,
		public bool $isUpload = false
	) {}
	
	public static function fromUploadedFile(UploadedFile $file): DocumentFile
	{
		return new DocumentFile
		(
			0,
			false,
			$file->getClientOriginalName(),
			0,
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
	
	public static function hydrate(array $data): static
    {
		return new DocumentFile
		(
			$data['school_id'],
			$data['isFolder'],
			$data['name'],
			$data['connection_id'],
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
	
	public function safeName()
	{
		return preg_replace('/[^a-zA-Z0-9\-_.]/', '', $this->name);
	}
	
	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
	
	public function toArray(): array
    {
		return
			[
				'school_id' => $this->school_id,
				'isFolder' => $this->isFolder,
				'name' => $this->name,
				'path' => $this->path,
				'mimeType' => $this->mimeType,
				'size' => $this->size,
				'connection_id' => $this->connection_id,
				'icon' => $this->icon,
				'canPreview' => $this->canPreview,
				'canDelete' => $this->canDelete,
				'canChangeName' => $this->canChangeName,
				'isUpload' => $this->isUpload,
			];
	}
	
	public function person(): Person
	{
		return Person::where('school_id', $this->school_id)
		             ->first();
	}
	
	public function preview(): string
	{
		return $this->connection()
		            ->previewFile($this);
	}
	
	public function connection(): ?DocumentFilesConnection
	{
		if($this->connection_id == 0) return null;
		return IntegrationConnection::where('id', $this->connection_id)
		                            ->first();
	}
	
	public function getExportFile(): ?ExportFile
	{
		if($this->isUpload)
		{
			$info = pathinfo($this->path);
			return new ExportFile
			(
				$this->name,
				File::get($this->path),
				$this->mimeType,
				$info['extension'],
				$this->size
			);
		}
		return $this->connection()
		            ->exportFile($this);
	}
}