<?php

namespace App\Models\Utilities;

use App\Classes\Settings\StorageSettings;
use App\Interfaces\Fileable;
use App\Jobs\OptimizeImageJob;
use App\Models\Integrations\IntegrationConnection;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

class WorkFile extends Model
{
	use HasUuids;

	public $timestamps = true;
	public $incrementing = false;
	protected $table = "work_files";
	protected $primaryKey = "id";
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
	
	protected static function booted(): void
	{
		static::created(function(WorkFile $workFile)
		{
			$workFile->url = route('settings.work.file', ['work_file' => $workFile->id]);
			$workFile->icon = $workFile->mimeType->icon;
			$workFile->save();
			//also, if the work file is an image, we will need to optimize it.
			if($workFile->mimeType->is_img)
				OptimizeImageJob::dispatch($workFile->id);
		});
		static::deleting(function(WorkFile $workFile)
		{
			$workFile->lmsConnection->deleteFile($workFile);
		});
	}

	public function canCreateThumb(): bool
	{
		return $this->mimeType->is_img;
	}

	public function hasThumbnail(): bool
	{
		return ($this->thumb_path != null);
	}

	public function generateThumb(): void
	{
		if($this->canCreateThumb())
		{
			$manager = new ImageManager(new Driver());
			$thmb = $manager->read($this->contents());
			if($thmb)
			{
				$thmb->pad(config('lms.thumb_max_height'), config('lms.thumb_max_height'));
				$this->thumb_path = $this->lmsConnection->storeThumbnail($this, $thmb->toJpeg());
				$this->save();
			}
		}
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
	
	public function isImage(): bool
	{
		return $this->mimeType->is_img;
	}

	public function isVideo(): bool
	{
		return $this->mimeType->is_video;
	}

	public function isAudio(): bool
	{
		return $this->mimeType->is_audio;
	}

	public function isDocument(): bool
	{
		return $this->mimeType->is_document;
	}

	public function mimeType(): BelongsTo
	{
		return $this->belongsTo(MimeType::class, 'mime');
	}
	
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

    public function contents(): string
    {
        return $this->lmsConnection->fileContents($this);
    }

	public function copyTo(Fileable $destination): WorkFile
	{
		$settings = app(StorageSettings::class);
		$connection = $settings->getWorkConnection($destination->getWorkStorageKey());
		return $connection->copyWorkFile($this, $destination);
	}

	public function fileable(): MorphTo
	{
		return $this->morphTo('fileable', 'fileable_type', $this->fileable_id? 'fileable_id': 'fileable_uuid');
	}
}
