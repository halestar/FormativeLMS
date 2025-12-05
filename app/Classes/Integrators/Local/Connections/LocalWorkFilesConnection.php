<?php

namespace App\Classes\Integrators\Local\Connections;

use App\Classes\Storage\DocumentFile;
use App\Interfaces\Fileable;
use App\Models\Integrations\Connections\WorkFilesConnection;
use App\Models\Utilities\WorkFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LocalWorkFilesConnection extends WorkFilesConnection
{
	
	public static function getSystemInstanceDefault(): array
	{
		return [];
	}

	
	public function persistFile(Fileable $fileable, DocumentFile $file, bool $hidden = false): ?WorkFile
	{
		$exportFile = $file->getExportFile();
		if(!$exportFile) return null;
		//generate a md5 name for the file.
		$fpath = $fileable->getWorkStorageKey()->value . "/" . uniqid() . '.' . $exportFile->extension;
		//persist the file.
		$path = Storage::disk($this->service->data->work_disk)
		               ->put($fpath, $exportFile->contents);
		//create the WorkFile
		$workFile = new WorkFile();
		$workFile->name = $exportFile->name;
		$workFile->connection_id = $this->id;
		$workFile->path = $fpath;
		$workFile->mime = $exportFile->mime;
		$workFile->size = $exportFile->size;
		$workFile->extension = $exportFile->extension;
		$workFile->invisible = $hidden;
		$workFile->public = $fileable->shouldBePublic();
		$workFile->save();
		//finally, we link the file
		$fileable->workFiles()->save($workFile);
		return $workFile;
	}
	
	public function deleteFile(WorkFile $file): void
	{
		Storage::disk($this->service->data->work_disk)
		       ->delete($file->path);
	}
	
	public function download(WorkFile $file): StreamedResponse
	{
		$headers =
			[
				'Content-Type: ' . $file->mime,
				'Content-Disposition: ' . ($file->shouldAttach() ? 'attachment; filename="' . $file->fileName() : 'inline'),
			];
		return Storage::disk($this->service->data->work_disk)
		              ->download
		              (
			              $file->path,
			              $file->name . "." . $file->extension,
			              $headers
		              );
	}
	
	public function fileContents(WorkFile $file): ?string
	{
		return Storage::disk($this->service->data->work_disk)
		              ->get($file->path);
	}

	public function copyWorkFile(WorkFile $file, Fileable $destination): WorkFile
	{
		//generate a md5 name for the file.
		$fpath = $destination->getWorkStorageKey()->value . "/" . uniqid() . '.' . $file->extension;
		//persist the file.
		$path = Storage::disk($this->service->data->work_disk)
		               ->put($fpath, $file->contents());
		//create the WorkFile
		$workFile = new WorkFile();
		$workFile->name = $file->name;
		$workFile->connection_id = $this->id;
		$workFile->path = $fpath;
		$workFile->mime = $file->mime;
		$workFile->size = $file->size;
		$workFile->extension = $file->extension;
		$workFile->invisible = $file->invisible;
		$workFile->public = $destination->shouldBePublic();
		$workFile->save();
		//finally, we link the file
		$destination->workFiles()->save($workFile);
		return $workFile;
	}

	public function cleanup(): void
	{
		// TODO: Implement cleanup() method.
	}
}