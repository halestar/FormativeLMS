<?php

namespace App\Classes\Storage\Work;

use App\Classes\Storage\DocumentFile;
use App\Interfaces\Fileable;
use App\Models\Utilities\WorkFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LocalWorkStorage extends WorkStorage
{
	
	public static function prettyName(): string
	{
		return __('settings.storage.work.local');
	}
	
	public static function instancePropertyName(): string
	{
		return __('settings.storage.work.local.instance');
	}
	
	public static function instancePropertyNameHelp(): string
	{
		return __('settings.storage.work.local.instance.help');
	}
	
	public function toArray()
	{
		return
			[
				'className' => LocalWorkStorage::class,
				'instanceProperty' => $this->instanceProperty,
				'displayName' => $this->displayName,
			];
	}
	
	public function persistFile(Fileable $fileable, DocumentFile $file): ?WorkFile
	{
		$exportFile = $file->getExportFile();
		if(!$exportFile) return null;
		//generate a md5 name for the file.
		$fpath = $this->instanceProperty . "/" . uniqid() . '.' . $exportFile->extension;
		//persist the file.
		$path = Storage::disk(config('lms.storage.work'))
		               ->put($fpath, $exportFile->contents);
		//create the WorkFile
		$workFile = new WorkFile();
		$workFile->name = $exportFile->name;
		$workFile->storage_instance = $this->instanceProperty;
		$workFile->path = $fpath;
		$workFile->mime = $exportFile->mime;
		$workFile->size = $exportFile->size;
		$workFile->extension = $exportFile->extension;
		$workFile->save();
		//finally, we link the file
		$fileable->workFiles()
		         ->attach($workFile);
		return $workFile;
	}
	
	public function deleteFile(WorkFile $file): void
	{
		Storage::disk(config('lms.storage.work'))
		       ->delete($file->path);
	}
	
	public function download(WorkFile $file): StreamedResponse
	{
		$headers =
			[
				'Content-Type: ' . $file->mime,
				'Content-Disposition: ' . ($file->shouldAttach() ? 'attachment; filename="' . $file->fileName() : 'inline'),
			];
		return Storage::disk(config('lms.storage.work'))
		              ->download
		              (
			              $file->path,
			              $file->name . "." . $file->extension,
			              $headers
		              );
	}
	
	protected function hydrateElements(array $data): void {}
}
