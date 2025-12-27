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
	final public static function getInstanceDefault(): array
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
	 * It should also delete the thumbnail file, if it exists.
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

	/**
	 * Similar to the download function, this function will download a thumbnail for a work file.
	 * @param WorkFile $file The file to download the thumbnail for.
	 * @return StreamedResponse
	 */
	abstract public function downloadThumb(WorkFile $file): StreamedResponse;

	/**
	 * Similar to the fileContents function, this function will get the contents of a thumbnail for a work file.
	 * @param WorkFile $file The file to get the thumbnail contents of.
	 * @return string|null The contents of the thumbnail, null if there is an error.
	 */
	abstract public function thumbContents(WorkFile $file): ?string;

	/**
	 * This function will copy a persisted work file from one fileable object to this one.
	 * How the file is copied is left to the integrator class.
	 * @param WorkFile $file The file to copy.
	 * @return WorkFile Returns the new file created.
	 */
	abstract public function copyWorkFile(WorkFile $file, Fileable $destination): WorkFile;

	/**
	 * This function is called when creating a thumbnail for a work file that CAN have a thumbnail (usually an image).
	 * This function will run on a script that will attempt to create a thumbnail for all work files that do not have one.
	 * @param WorkFile $workFile The work file to create a thumbnail for.
	 * @param string $contents The contents of the thumbnail file to persist. Contents are always in JPEG format
	 * @return string The path to the thumbnail file.
	 */
	abstract public function storeThumbnail(WorkFile $workFile, string $contents): string;

	/**
	 * Shit happens. Sometimes objects don't get deleted properly, sometimes some errors in other
	 * parts of the code lead to links not happening, etc. This function will attempt to clean up
	 * your work files by doing two things:
	 * 1. Deleting any of your files that do not have an entry in the work_files table.
	 * 2. Attempting to load every work file in your connection and verify that it is linked to a fileable object,
	 * and that the fileable object still exists. If it does not, delete the work file and the corresponding file in your
	 * storage.
	 * This function only runs on a script, so it can be slow if needed. You may also choose to only execute parts of
	 * function at different intervals. You can do this by setting a last_cleaned timestamp in the data
	 * section of the connection.
	 * @return void
	 */
	abstract public function cleanup(): void;
	
}