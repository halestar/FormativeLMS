<?php

namespace App\Classes\Storage\Document;

use App\Classes\Storage\DocumentFile;
use App\Classes\Storage\ExportFile;
use App\Models\People\Person;
use App\Models\Utilities\MimeType;
use Google\Client;
use Google\Service\Drive\DriveFile;
use Google_Service_Drive;
use Illuminate\Http\UploadedFile;

class GoogleDocumentStorage extends DocumentStorage
{
	protected Client $client;
	
	public function __construct(string $displayName)
	{
		parent::__construct($displayName);
		$this->client = new Client();
		$this->client->setClientId(config('services.google.client_id'));
		$this->client->setClientSecret(config('services.google.client_secret'));
	}
	
	public static function prettyName(): string
	{
		return __('settings.storage.documents.google');
	}
	
	/**
	 * @inheritDoc
	 */
	public function toArray()
	{
		return
			[
				'className' => GoogleDocumentStorage::class,
				'instanceProperty' => $this->instanceProperty,
				'displayName' => $this->displayName,
			];
	}
	
	/**
	 * @inheritDoc
	 */
	public function rootFiles(Person $person, array $mimeTypes = []): array
	{
		$drive = $this->personalDrive($person);
		$optParams =
			[
				'q' => "'root' in parents and trashed=false",
				'fields' => 'nextPageToken, files(id, name, mimeType, thumbnailLink, fullFileExtension)',
				'pageSize' => 100,
			];
		
		$results = $drive->files->listFiles($optParams);
		$documentFiles = [];
		foreach($results->getFiles() as $gFile)
			$documentFiles[] = $this->createDocumentFile($person, $gFile);
		return $documentFiles;
	}
	
	private function personalDrive(Person $person): Google_Service_Drive
	{
		$authDriver = $person->auth_driver;
		$authDriver->refreshToken();
		$settings = $authDriver->getPasswordSettings();
		$this->client->setAccessToken($settings['oauth_token']);
		return new Google_Service_Drive($this->client);
	}
	
	private function createDocumentFile(Person $person, DriveFile $file): ?DocumentFile
	{
		if($file->mimeType == 'application/vnd.google-apps.folder') {
			//make a folder
			return new DocumentFile
			(
				$person->school_id,
				true,
				$file->name,
				$this->instanceProperty,
				$file->id,
				MimeType::FOLDER_HTML,
				'',
				0,
				false,
				false,
				false,
				false
			);
		}
		return new DocumentFile
		(
			$person->school_id,
			false,
			$file->name,
			$this->instanceProperty,
			$file->id,
			MimeType::find($file->mimeType)->icon,
			$file->mimeType,
			$file->size ?? 0,
			false,
			false,
			false,
			false
		);
	}
	
	/**
	 * @inheritDoc
	 */
	public function files(Person $person, DocumentFile $directory, array $mimeTypes = []): array
	{
		$drive = $this->personalDrive($person);
		$optParams =
			[
				'q' => "'" . $directory->path . "' in parents and trashed=false",
				'fields' => 'nextPageToken, files(id, name, mimeType, thumbnailLink, fullFileExtension, parents)',
				'pageSize' => 100,
			];
		
		$results = $drive->files->listFiles($optParams);
		$documentFiles = [];
		foreach($results->getFiles() as $gFile)
			$documentFiles[] = $this->createDocumentFile($person, $gFile);
		return $documentFiles;
	}
	
	/**
	 * @inheritDoc
	 */
	public function file(Person $person, string $path): ?DocumentFile
	{
		$drive = $this->personalDrive($person);
		$file = $drive->files->get($path, ['fields' => 'id, name, mimeType, thumbnailLink, fullFileExtension']);
		return $this->createDocumentFile($person, $file);
	}
	
	/**
	 * @inheritDoc
	 */
	public function parentDirectory(Person $person, DocumentFile $file): ?DocumentFile
	{
		$drive = $this->personalDrive($person);
		$file = $drive->files->get($file->path, ['fields' => 'parents']);
		if(!$file->parent | !is_array($file->parents) || count($file->parents) == 0)
			return null;
		$parent_id = $file->parents[0];
		$file = $drive->files->get($parent_id, ['fields' => 'id, name, mimeType, thumbnailLink, fullFileExtension']);
		return $this->createDocumentFile($person, $file);
	}
	
	/**
	 * @inheritDoc
	 */
	public function previewFile(Person $person, DocumentFile $file): string
	{
		return '';
	}
	
	/**
	 * @inheritDoc
	 */
	public function deleteFile(Person $person, DocumentFile $file): void {}
	
	/**
	 * @inheritDoc
	 */
	public function changeName(Person $person, DocumentFile $file, string $name): void {}
	
	/**
	 * @inheritDoc
	 */
	public function changeParent(Person $person, DocumentFile $file, DocumentFile $newParent = null): void {}
	
	/**
	 * @inheritDoc
	 */
	public function canPersistFiles(): bool
	{
		return false;
	}
	
	/**
	 * @inheritDoc
	 */
	public function persistFolder(Person $person, string $name, DocumentFile $parent = null): ?DocumentFile
	{
		return null;
	}
	
	/**
	 * @inheritDoc
	 */
	public function persistFile(Person $person, UploadedFile $file, DocumentFile $parent = null): ?DocumentFile
	{
		return null;
	}
	
	/**
	 * @inheritDoc
	 */
	public function exportFile(Person $person, DocumentFile $file, array $preferMime = []): ?ExportFile
	{
		$drive = $this->personalDrive($person);
		//we can do a straight download if the file is an image file or a pdf
		if(str_starts_with($file->mimeType, 'image/') || $file->mimeType == 'application/pdf') {
			$response = $drive->files->get($file->path, ['alt' => 'media']);
			$fileContents = $response->getBody()
			                         ->getContents();
			switch($file->mimeType) {
				case 'image/jpeg':
					$ext = "jpg";
					break;
				case 'image/png':
					$ext = "png";
					break;
				case 'image/gif':
					$ext = "gif";
					break;
				case 'application/pdf':
					$ext = "pdf";
					break;
				default:
					$ext = "pdf";
			}
			return new ExportFile($file->name, $fileContents, $file->mimeType, $ext, $file->size);
		}
		//any other kind of file, we download as a PDF
		$response = $drive->files->export($file->path, 'application/pdf', ['alt' => 'media']);
		$fileContents = $response->getBody()
		                         ->getContents();
		return new ExportFile($file->name, $fileContents, 'application/pdf', 'pdf', $file->size);
	}
	
	protected function hydrateElements(array $data): void {}
}