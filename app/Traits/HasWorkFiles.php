<?php

namespace App\Traits;

use App\Interfaces\Fileable;
use App\Models\Utilities\WorkFile;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasWorkFiles
{
	public static function bootHasWorkFiles()
	{
		static::deleting(function(Fileable $fileable)
		{
			foreach($fileable->workFiles as $file)
				$file->delete();
		});
	}
	public function workFiles(): MorphMany
	{
		$relatedFK = ($this->getKeyType() == 'string')? "fileable_uuid": "fileable_id";
		return $this->morphMany(WorkFile::class, 'fileable', 'fileable_type', $relatedFK);
	}

	public function copyWorkFilesFrom(Fileable $source): void
	{
		foreach($source->workFiles as $file)
			$file->copyTo($this);
	}

	public function copyWorkFilesTo(Fileable $target): void
	{
		foreach($this->workFiles as $file)
			$file->copyTo($target);
	}

	public function hasFiles(): bool
	{
		return $this->workFiles()->count() > 0;
	}
}
