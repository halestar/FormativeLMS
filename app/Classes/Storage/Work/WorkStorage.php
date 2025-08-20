<?php

namespace App\Classes\Storage\Work;

use App\Classes\Storage\DocumentFile;
use App\Classes\Storage\LmsStorage;
use App\Interfaces\Fileable;
use App\Models\Utilities\WorkFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class WorkStorage extends LmsStorage
{
	abstract public function persistFile(Fileable $fileable, DocumentFile $file): ?WorkFile;
	
	abstract public function deleteFile(WorkFile $file): void;
	
	abstract public function download(WorkFile $file): StreamedResponse;
	
}