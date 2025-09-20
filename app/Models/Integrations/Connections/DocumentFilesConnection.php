<?php

namespace App\Models\Integrations\Connections;

use App\Classes\Storage\DocumentFile;
use App\Classes\Storage\ExportFile;
use App\Interfaces\IntegrationConnectionInterface;
use App\Models\Integrations\IntegrationConnection;
use Illuminate\Http\UploadedFile;

abstract class DocumentFilesConnection extends IntegrationConnection implements IntegrationConnectionInterface
{
	/**
	 * @param array $mimeTypes The mime types allowed. If empty, all mime types are allowed.
	 * @return array An array of DocumentFile objects of all the files at the root of the storage.
	 */
	abstract public function rootFiles(array $mimeTypes = []): array;
	
	/**
	 * @param DocumentFile $directory The directory to get the files from.
	 * @param array $mimeTypes The mime types allowed. If empty, all mime types are allowed.
	 * @return array An array of DocumentFile objects of all the files in the directory.
	 */
	abstract public function files(DocumentFile $directory, array $mimeTypes = []): array;
	
	/**
	 * @param string $path The path to the file.
	 * @return DocumentFile|null The file object, null if it does not exist.
	 */
	abstract public function file(string $path): ?DocumentFile;
	
	/**
	 * @param DocumentFile $file The file to get the parent directory of.
	 * @return DocumentFile|null The parent directory, null if it is the root directory.
	 */
	abstract public function parentDirectory(DocumentFile $file): ?DocumentFile;
	
	/**
	 * @param DocumentFile $file The file to get the preview of.
	 * @return string An html string to display a preview of the file, assuming it is previewable.
	 */
	abstract public function previewFile(DocumentFile $file): string;
	
	/**
	 * @param DocumentFile $file The file to delete.
	 * @return void
	 */
	abstract public function deleteFile(DocumentFile $file): void;
	
	/**
	 * @param DocumentFile $file The file to change the name of.
	 * @param string $name The new name of the file.
	 * @return void
	 */
	abstract public function changeName(DocumentFile $file, string $name): void;
	
	/**
	 * @param DocumentFile $file The file we're moving
	 * @param DocumentFile|null $newParent The new parent directory of the file.
	 * @return void
	 */
	abstract public function changeParent(DocumentFile $file, DocumentFile $newParent = null): void;
	
	/**
	 * @return bool True if the storage can persist files or folders, false otherwise.
	 */
	abstract public function canPersistFiles(): bool;
	
	/**
	 * @param string $name The name of the folder to create.
	 * @param DocumentFile|null $parent The parent directory of the folder.
	 * @return DocumentFile|null Returns the new folder created, null if the folder already exists.
	 */
	abstract public function persistFolder(string $name, DocumentFile $parent = null): ?DocumentFile;
	
	/**
	 * @param UploadedFile $file The file that was uploaded
	 * @param DocumentFile|null $parent The parent directory to store the file under.
	 * @return DocumentFile|null Returns the new file created, null if there is an error
	 */
	abstract public function persistFile(UploadedFile $file, DocumentFile $parent = null): ?DocumentFile;
	
	/**
	 * @param DocumentFile $file The file to download.
	 * @param array $preferMime Optional array of preferred mime types.
	 * @return ExportFile Contains the file data and metadata for the exported file, null if the file doesn't exists
	 */
	abstract public function exportFile(DocumentFile $file, array $preferMime = []): ?ExportFile;
	
}