<?php

namespace App\Classes\Storage\Document;

use App\Classes\Storage\DocumentFile;
use App\Classes\Storage\ExportFile;
use App\Classes\Storage\LmsStorage;
use App\Models\People\Person;
use Illuminate\Http\UploadedFile;

abstract class DocumentStorage extends LmsStorage
{
	/**
	 * @param Person $person The person to get the storage from.
	 * @param array $mimeTypes The mime types allowed. If empty, all mime types are allowed.
	 * @return array An array of DocumentFile objects of all the files at the root of the storage.
	 */
	abstract public function rootFiles(Person $person, array $mimeTypes = []): array;
	
	/**
	 * @param Person $person The person to get the storage from.
	 * @param DocumentFile $directory The directory to get the files from.
	 * @param array $mimeTypes The mime types allowed. If empty, all mime types are allowed.
	 * @return array An array of DocumentFile objects of all the files in the directory.
	 */
	abstract public function files(Person $person, DocumentFile $directory, array $mimeTypes = []): array;
	
	/**
	 * @param Person $person The person to get the storage from.
	 * @param string $path The path to the file.
	 * @return DocumentFile|null The file object, null if it does not exist.
	 */
	abstract public function file(Person $person, string $path): ?DocumentFile;
	
	/**
	 * @param Person $person The person to get the storage from.
	 * @param DocumentFile $file The file to get the parent directory of.
	 * @return DocumentFile|null The parent directory, null if it is the root directory.
	 */
	abstract public function parentDirectory(Person $person, DocumentFile $file): ?DocumentFile;
	
	/**
	 * @param Person $person The person to get the storage from.
	 * @param DocumentFile $file The file to get the preview of.
	 * @return string An html string to display a preview of the file, assuming it is previewable.
	 */
	abstract public function previewFile(Person $person, DocumentFile $file): string;
	
	/**
	 * @param Person $person The person to get the storage from.
	 * @param DocumentFile $file The file to delete.
	 * @return void
	 */
	abstract public function deleteFile(Person $person, DocumentFile $file): void;
	
	/**
	 * @param Person $person The person to get the storage from.
	 * @param DocumentFile $file The file to change the name of.
	 * @param string $name The new name of the file.
	 * @return void
	 */
	abstract public function changeName(Person $person, DocumentFile $file, string $name): void;
	
	/**
	 * @param Person $person The person to get the storage from.
	 * @param DocumentFile $file The file we're moving
	 * @param DocumentFile|null $newParent The new parent directory of the file.
	 * @return void
	 */
	abstract public function changeParent(Person $person, DocumentFile $file, DocumentFile $newParent = null): void;
	
	/**
	 * @return bool True if the storage can persist files or folders, false otherwise.
	 */
	abstract public function canPersistFiles(): bool;
	
	/**
	 * @param Person $person The person to get the storage from.
	 * @param string $name The name of the folder to create.
	 * @param DocumentFile|null $parent The parent directory of the folder.
	 * @return DocumentFile|null Returns the new folder created, null if the folder already exists.
	 */
	abstract public function persistFolder(Person $person, string $name, DocumentFile $parent = null): ?DocumentFile;
	
	/**
	 * @param Person $person The person to get the storage from.
	 * @param UploadedFile $file The file that was uploaded
	 * @param DocumentFile|null $parent The parent directory to store the file under.
	 * @return DocumentFile|null Returns the new file created, null if there is an error
	 */
	abstract public function persistFile(Person $person, UploadedFile $file,
		DocumentFile $parent = null): ?DocumentFile;
	
	/**
	 * @param Person $person The person to get the storage from.
	 * @param DocumentFile $file The file to download.
	 * @param array $preferMime Optional array of preferred mime types.
	 * @return ExportFile Contains the file data and metadata for the exported file, null if the file doesn't exists
	 */
	abstract public function exportFile(Person $person, DocumentFile $file, array $preferMime = []): ?ExportFile;
	
}