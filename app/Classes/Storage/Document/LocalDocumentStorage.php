<?php

namespace App\Classes\Storage\Document;

use App\Classes\Storage\DocumentFile;
use App\Classes\Storage\ExportFile;
use App\Models\People\Person;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LocalDocumentStorage extends DocumentStorage
{
	private const UNKN_PREVIEW = '<div class="display-1"><i class="fa-solid fa-file-circle-question"></i></div>';
	
	public static function prettyName(): string
	{
		return __('settings.storage.documents.local');
	}
	
	public function toArray()
	{
		return
			[
				'className' => LocalDocumentStorage::class,
				'instanceProperty' => $this->instanceProperty,
				'displayName' => $this->displayName,
			];
	}
	
	public function rootFiles(Person $person, array $mimeTypes = []): array
	{
		return $this->readDirectory($person, mimeFilter: $mimeTypes);
	}
	
	private function readDirectory(Person $person, DocumentFile $directory = null, array $mimeFilter = []): array
	{
		$files = [];
		$basePath = $this->instanceProperty . "/" . $person->school_id;
		$folderPath = '';
		if($directory && $directory->isFolder)
			$folderPath = "/" . $directory->path;
		foreach(Storage::disk(config('lms.storage.documents'))
		               ->directories($basePath . $folderPath) as $file) {
			$fPath = str_replace($basePath, "", $file);
			$f = $this->createDocumentFile($person, $fPath);
			if($f)
				$files[] = $f;
		}
		foreach(Storage::disk(config('lms.storage.documents'))
		               ->files($basePath . $folderPath) as $file) {
			$fPath = str_replace($basePath, "", $file);
			$f = $this->createDocumentFile($person, $fPath);
			if($f && (in_array($f->mimeType, $mimeFilter) || count($mimeFilter) === 0))
				$files[] = $f;
		}
		return $files;
	}
	
	private function createDocumentFile(Person $person, string $path): ?DocumentFile
	{
		$basePath = Storage::disk(config('lms.storage.documents'))
		                   ->path($this->instanceProperty . "/" .
			                   $person->school_id);
		$realpath = $basePath . $path;
		if(!File::exists($realpath)) return null;
		$isDir = File::isDirectory($realpath);
		$mimeType = File::mimeType($realpath);
		$icon = $isDir ? config('file_icons.folder', '') :
			config('file_icons.' . $mimeType, config('file_icons.default', ''));
		$canPreview = !$isDir && (substr($mimeType, 0, 6) === 'image/');
		$file = new DocumentFile
		(
			$person->school_id,
			File::isDirectory($realpath),
			File::name($realpath),
			$this->instanceProperty,
			$path,
			$icon,
			$mimeType,
			File::size($realpath),
			$canPreview,
			true,
			true
		);
		return $file;
	}
	
	public function files(Person $person, DocumentFile $directory, array $mimeTypes = []): array
	{
		return $this->readDirectory($person, $directory, $mimeTypes);
	}
	
	public function file(Person $person, string $path): ?DocumentFile
	{
		return $this->createDocumentFile($person, $path);
	}
	
	public function parentDirectory(Person $person, DocumentFile $file): ?DocumentFile
	{
		$basePath = $this->instanceProperty . "/" . $person->school_id;
		$realBasePath = Storage::disk(config('lms.storage.documents'))
		                       ->path($basePath);
		$realPath = Storage::disk(config('lms.storage.documents'))
		                   ->path($basePath . "/" . $file->path);
		$parentPath = dirname($realPath);
		if($parentPath === $realBasePath) return null;
		return $this->createDocumentFile($person, str_replace($realBasePath, "", $parentPath));
	}
	
	public function previewFile(Person $person, DocumentFile $file): string
	{
		if(!$file->canPreview) return LocalDocumentStorage::UNKN_PREVIEW;
		$instancePath = $this->instanceProperty . "/" . $person->school_id;
		$basePath = Storage::disk(config('lms.storage.documents'))
		                   ->path($instancePath);
		$realpath = $basePath . $file->path;
		if(!File::exists($realpath) ||
			File::isDirectory($realpath) ||
			(substr(File::mimeType($realpath), 0, 5) === 'image/')) {
			return LocalDocumentStorage::UNKN_PREVIEW;
		}
		
		return '<img src="data:' . File::mimeType($realpath) . ';base64,' .
			base64_encode(Storage::disk(config('lms.storage.documents'))
			                     ->get($instancePath . "/" . $file->path)) .
			'" alt="' . $file->name . '" />';
	}
	
	public function deleteFile(Person $person, DocumentFile $file): void
	{
		$instancePath = $this->instanceProperty . "/" . $person->school_id;
		if($file->isFolder)
			Storage::disk(config('lms.storage.documents'))
			       ->deleteDirectory($instancePath . "/" . $file->path);
		else
			Storage::disk(config('lms.storage.documents'))
			       ->delete($instancePath . "/" . $file->path);
	}
	
	public function changeName(Person $person, DocumentFile $file, string $name): void
	{
		$originalPath = $this->instanceProperty . "/" . $person->school_id . "/" . $file->path;
		$info = pathinfo($originalPath);
		$newPath = $info['dirname'] . "/" . $name;
		if(!$file->isFolder)
			$newPath .= "." . $info['extension'];
		Storage::disk(config('lms.storage.documents'))
		       ->move($originalPath, $newPath);
		$file->name = $name;
		$file->path = $newPath;
	}
	
	public function canPersistFiles(): bool
	{
		return true;
	}
	
	public function persistFolder(Person $person, string $name, DocumentFile $parent = null): ?DocumentFile
	{
		$basePath = $this->instanceProperty . "/" . $person->school_id . "/";
		if($parent)
			$basePath .= $parent->path . "/";
		$basePath .= $name;
		if(Storage::disk(config('lms.storage.documents'))
		          ->exists($basePath))
			return null;
		$path = Storage::disk(config('lms.storage.documents'))
		               ->makeDirectory($basePath);
		return $this->createDocumentFile($person, $path);
	}
	
	public function changeParent(Person $person, DocumentFile $file, DocumentFile $newParent = null): void
	{
		$originalPath = $this->instanceProperty . "/" . $person->school_id . "/" . $file->path;
		$info = pathinfo($originalPath);
		if(!$newParent)
			$newPath = $this->instanceProperty . "/" . $person->school_id . "/" . $info['filename'];
		else
			$newPath = $this->instanceProperty . "/" . $person->school_id . "/" . $newParent->path . "/" . $info['filename'];
		Storage::disk(config('lms.storage.documents'))
		       ->move($originalPath, $newPath);
		$file->path = $newPath;
	}
	
	public function persistFile(Person $person, UploadedFile $file, DocumentFile $parent = null): ?DocumentFile
	{
		$basePath = $this->instanceProperty . "/" . $person->school_id;
		if($parent)
			$basePath .= $parent->path;
		$path = Storage::disk(config('lms.storage.documents'))
		               ->putFileAs($basePath, $file, $file->getClientOriginalName());
		return $this->createDocumentFile($person, $path);
	}
	
	public function exportFile(Person $person, DocumentFile $file, array $preferMime = []): ?ExportFile
	{
		$instancePath = $this->instanceProperty . "/" . $person->school_id;
		$basePath = Storage::disk(config('lms.storage.documents'))
		                   ->path($instancePath);
		$realpath = $basePath . $file->path;
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
	
	protected function hydrateElements(array $data): void {}
}
