<?php

namespace App\Models\Integrations\Connections;

use App\Classes\Storage\DocumentFile;
use App\Interfaces\Fileable;
use App\Interfaces\IntegrationConnectionInterface;
use App\Models\Integrations\IntegrationConnection;
use App\Models\Utilities\WorkFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class WorkFilesConnection extends IntegrationConnection implements IntegrationConnectionInterface
{
	public static function getInstanceDefault(): array
	{
		return [];
	}
	/**
	 * This function will persis a file to the system storage. The file will be filed under the storage
	 * provided by the Fileable object.  THe work document will also be linked to the fileable object.
	 * @param Fileable $fileable The object that we will be storing the file for.
	 * @param DocumentFile $file The file to store.
	 * @param bool $hidden Whether the file should be hidden from the user.
	 * @return WorkFile|null Returns an instance of the new file created, null if there is an error.
	 */
	abstract public function persistFile(Fileable $fileable, DocumentFile $file, bool $hidden = false): ?WorkFile;
	
	/**
	 * This function will delete a persisted file from the system storage.
	 * @param WorkFile $file The file to delete.
	 * @return void
	 */
	abstract public function deleteFile(WorkFile $file): void;
	
	/**
	 * This function will download a persisted file from the system storage.
	 * @param WorkFile $file The file to download.
	 * @return StreamedResponse The response to download the file.
	 */
	abstract public function download(WorkFile $file): StreamedResponse;
	
	/**
	 * This function will get the contents of a persisted file from the system storage.
	 * @param WorkFile $file The file to get the contents of.
	 * @return string|null The contents of the file, null if there is an error.
	 */
	abstract public function fileContents(WorkFile $file): ?string;
	
}