<?php

namespace App\Models\Utilities;

use App\Models\Integrations\IntegrationConnection;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
			'connection_id',
			'path',
			'mime',
			'size',
			'extension',
			'url',
			'icon',
			'invisible',
			'public',
		];
	
	protected function casts(): array
	{
		return
			[
				'invisible' => 'boolean',
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
			$workFile->icon = $workFile->mimeType->icon;
			$workFile->save();
		});
		static::deleting(function(WorkFile $workFile) {
			$workFile->lmsConnection->deleteFile($workFile);
		});
	}
	
	public function lmsConnection(): BelongsTo
	{
		return $this->belongsTo(IntegrationConnection::class, 'connection_id');
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
	protected function invisible(Builder $query): void
	{
		$query->where('invisible', true);
	}
	
	#[Scope]
	protected function visible(Builder $query): void
	{
		$query->where('invisible', false);
	}
	
	#[Scope]
	protected function public(Builder $query): void
	{
		$query->where('public', true);
	}
	
	public function isImage(): bool
	{
		return $this->mimeType->is_img;
	}
	
	public function mimeType(): BelongsTo
	{
		return $this->belongsTo(MimeType::class, 'mime');
	}
}
