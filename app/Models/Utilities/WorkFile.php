<?php

namespace App\Models\Utilities;

use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\Work\WorkStorage;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class WorkFile extends Model
{
	use HasUuids;
	public $timestamps = true;
	protected $table = "work_files";
	protected $primaryKey = "id";
	public $incrementing = false;
	protected $keyType = 'string';
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
			'hidden',
			'public',
		];
	
	protected function casts(): array
	{
		return
			[
				'hidden' => 'boolean',
				'public' => 'boolean',
				'created_at' => 'datetime: m/d/Y h:i A',
				'updated_at' => 'datetime: m/d/Y h:i A',
			];
	}
	
	protected static function booted(): void
	{
		static::created(function(WorkFile $workFile) {
			if($workFile->public)
				$workFile->url = route('settings.work.file.public', ['work_file' => $workFile->id]);
			else
				$workFile->url = route('settings.work.file.private', ['work_file' => $workFile->id]);
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
	
	#[Scope]
	protected function hidden(Builder $query): void
	{
		$query->where('hidden', true);
	}
	
	#[Scope]
	protected function shown(Builder $query): void
	{
		$query->where('hidden', false);
	}
	
	#[Scope]
	protected function public(Builder $query): void
	{
		$query->where('public', true);
	}
	
}
