<?php

namespace App\Classes\Storage\Document;

use App\Classes\Storage\DocumentFile;
use App\Classes\Storage\LmsStorage;
use App\Models\People\Person;
use Illuminate\Http\UploadedFile;

abstract class DocumentStorage extends LmsStorage
{
	abstract public function rootFiles(Person $person, array $mimeTypes = []): array;
	abstract public function files(Person $person, DocumentFile $directory, array $mimeTypes = []): array;
	abstract public function file(Person $person, string $path): ?DocumentFile;
	abstract public function parentDirectory(Person $person, DocumentFile $file): ?DocumentFile;
	abstract public function previewFile(Person $person, DocumentFile $file): string;
	abstract public function deleteFile(Person $person, DocumentFile $file): void;
	abstract public function changeName(Person $person, DocumentFile $file, string $name): void;
	abstract public function changeParent(Person $person, DocumentFile $file, DocumentFile $newParent = null): void;
	abstract public function canPersistFiles(): bool;
	abstract public function persistFolder(Person $person, string $name, DocumentFile $parent = null): void;
	abstract public function persistFile(Person $person, UploadedFile $file, DocumentFile $parent = null): void;
}