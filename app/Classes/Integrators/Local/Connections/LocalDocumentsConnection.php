<?php

namespace App\Classes\Integrators\Local\Connections;

use App\Classes\Storage\DocumentFile;
use App\Classes\Storage\ExportFile;
use App\Models\Integrations\Connections\DocumentFilesConnection;
use App\Models\Utilities\MimeType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LocalDocumentsConnection extends DocumentFilesConnection
{
	private const UNKN_PREVIEW = '<div class="display-1"><i class="fa-solid fa-file-circle-question"></i></div>';
	
	/***************************************
	 * PRIVATE PROPERTIES AND METHODS
	 */
	
	private function readDirectory(DocumentFile $directory = null, array $mimeFilter = []): array
	{
		$files = [];
		$basePath = $this->person->school_id;
		$folderPath = '';
		if($directory && $directory->isFolder)
			$folderPath = "/" . $directory->path;
		foreach(Storage::disk($this->service->data->documents_disk)
		               ->directories($basePath . $folderPath) as $file)
		{
			$fPath = str_replace($basePath, "", $file);
			$f = $this->createDocumentFile($fPath);
			if($f)
				$files[] = $f;
		}
		foreach(Storage::disk($this->service->data->documents_disk)
		               ->files($basePath . $folderPath) as $file)
		{
			$fPath = str_replace($basePath, "", $file);
			$f = $this->createDocumentFile($fPath);
			if($f && (in_array($f->mimeType, $mimeFilter) || count($mimeFilter) === 0))
				$files[] = $f;
		}
		return $files;
	}
	
	private function createDocumentFile(string $path): ?DocumentFile
	{
		$realpath = Storage::disk($this->service->data->documents_disk)
		                   ->path($this->person->school_id) . $path;
		if(!File::exists($realpath)) return null;
		$isDir = File::isDirectory($realpath);
		$mimeType = File::mimeType($realpath);
		$icon = $isDir ? MimeType::FOLDER_HTML : MimeType::find($mimeType)->icon;
		$canPreview = !$isDir && (substr($mimeType, 0, 6) === 'image/');
		return new DocumentFile
		(
			$this->person->school_id,
			File::isDirectory($realpath),
			File::name($realpath),
			$this->id,
			$path,
			$icon,
			$mimeType,
			File::size($realpath),
			$canPreview,
			true,
			true
		);
	}
	
	/**
	 * @inheritDoc
	 */
	public function rootFiles(array $mimeTypes = []): array
	{
		return $this->readDirectory(mimeFilter: $mimeTypes);
	}
	
	/**
	 * @inheritDoc
	 */
	public function files(DocumentFile $directory, array $mimeTypes = []): array
	{
		return $this->readDirectory($directory, $mimeTypes);
	}
	
	/**
	 * @inheritDoc
	 */
	public function file(string $path): ?DocumentFile
	{
		return $this->createDocumentFile($path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function parentDirectory(DocumentFile $file): ?DocumentFile
	{
		$basePath = $this->person->school_id;
		$realBasePath = Storage::disk($this->service->data->documents_disk)
		                       ->path($basePath);
		$realPath = Storage::disk($this->service->data->documents_disk)
		                   ->path($basePath . "/" . $file->path);
		$parentPath = dirname($realPath);
		if($parentPath === $realBasePath) return null;
		return $this->createDocumentFile(str_replace($realBasePath, "", $parentPath));
	}
	
	/**
	 * @inheritDoc
	 */
	public function previewFile(DocumentFile $file): string
	{
		if(!$file->canPreview) return self::UNKN_PREVIEW;
		$realpath = Storage::disk($this->service->data->documents_disk)
		                   ->path($this->person->school_id) . $file->path;
		if(!File::exists($realpath) ||
			File::isDirectory($realpath) ||
			(substr(File::mimeType($realpath), 0, 5) === 'image/'))
		{
			return self::UNKN_PREVIEW;
		}
		
		return '<img src="data:' . File::mimeType($realpath) . ';base64,' .
			base64_encode(Storage::disk($this->service->data->documents_disk)
			                     ->get($file->path)) .
			'" alt="' . $file->name . '" />';
	}
	
	/**
	 * @inheritDoc
	 */
	public function deleteFile(DocumentFile $file): void
	{
		$instancePath = $this->person->school_id . "/" . $file->path;
		if($file->isFolder)
			Storage::disk($this->service->data->documents_disk)
			       ->deleteDirectory($instancePath);
		else
			Storage::disk($this->service->data->documents_disk)
			       ->delete($instancePath);
	}
	
	/**
	 * @inheritDoc
	 */
	public function changeName(DocumentFile $file, string $name): void
	{
		$originalPath = $this->person->school_id . "/" . $file->path;
		$info = pathinfo($originalPath);
		$newPath = $info['dirname'] . "/" . $name;
		if(!$file->isFolder)
			$newPath .= "." . $info['extension'];
		Storage::disk($this->service->data->documents_disk)
		       ->move($originalPath, $newPath);
		$file->name = $name;
		$file->path = $newPath;
	}
	
	/**
	 * @inheritDoc
	 */
	public function changeParent(DocumentFile $file, DocumentFile $newParent = null): void
	{
		$originalPath = $this->person->school_id . "/" . $file->path;
		$info = pathinfo($originalPath);
		if(!$newParent)
			$newPath = $this->person->school_id . "/" . $info['filename'];
		else
			$newPath = $this->person->school_id . "/" . $newParent->path . "/" . $info['filename'];
		Storage::disk($this->service->data->documents_disk)
		       ->move($originalPath, $newPath);
		$file->path = $newPath;
	}
	
	/**
	 * @inheritDoc
	 */
	public function canPersistFiles(): bool
	{
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	public function persistFolder(string $name, DocumentFile $parent = null): ?DocumentFile
	{
		$basePath = $this->person->school_id . "/";
		if($parent)
			$basePath .= $parent->path . "/";
		$basePath .= $name;
		if(Storage::disk($this->service->data->documents_disk)
		          ->exists($basePath))
			return null;
		$path = Storage::disk($this->service->data->documents_disk)
		               ->makeDirectory($basePath);
		return $this->createDocumentFile($path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function persistFile(UploadedFile $file, DocumentFile $parent = null): ?DocumentFile
	{
		$basePath = $this->person->school_id;
		if($parent)
			$basePath .= $parent->path;
		$path = Storage::disk($this->service->data->documents_disk)
		               ->putFileAs($basePath, $file, $file->getClientOriginalName());
		return $this->createDocumentFile($path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function exportFile(DocumentFile $file, array $preferMime = []): ?ExportFile
	{
		$instancePath = $this->person->school_id;
		$realpath = Storage::disk($this->service->data->documents_disk)
		                   ->path($instancePath) . $file->path;
		if(!File::exists($realpath)) return null;
		return new ExportFile
		(
			$file->name,
			File::get($realpath),
			File::mimeType($realpath),
			File::extension($realpath),
			File::size($realpath)
		);
	}
	
	public static function getSystemInstanceDefault(): array
	{
		return [];
	}
	
	public static function getInstanceDefault(): array
	{
		return [];
	}
}