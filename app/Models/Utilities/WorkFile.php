<?php

namespace App\Models\Utilities;

use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\Work\WorkStorage;
use Illuminate\Database\Eloquent\Model;

class WorkFile extends Model
{
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "work_files";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'name',
			'storage_instance',
			'path',
			'mime',
			'size',
			'extension',
			'url',
			'icon',
		];
	
	protected static function booted(): void
	{
		static::created(function(WorkFile $workFile) {
			$workFile->url = route('settings.work.file.get', ['work_file' => $workFile->id]);
			$workFile->icon = config('file_icons.' . $workFile->mime, config('file_icons.default'));
			$workFile->save();
		});
		static::deleting(function(WorkFile $workFile) {
			$storageInstance = $workFile->storageInstance();
			$storageInstance->deleteFile($workFile);
		});
	}
	
	public function storageInstance(): WorkStorage
	{
		$storageSettings = app()->make(StorageSettings::class);
		return $storageSettings->getInstance($this->storage_instance);
	}
	
	public function shouldAttach(): bool
	{
		return !(str_starts_with($this->mime, 'image/'));
	}
	
	public function fileName(): string
	{
		return $this->name . "." . $this->extension;
	}
	
}
