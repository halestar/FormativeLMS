<?php

namespace App\Traits;

use App\Interfaces\Fileable;
use App\Models\Utilities\WorkFile;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasWorkFiles
{
	public function workFiles(): MorphToMany|BelongsToMany
	{
		$relatedFK = ($this->getKeyType() == 'string')? "fileable_uuid": "fileable_id";
		return $this->morphToMany(WorkFile::class, 'fileable', 'fileables', $relatedFK, 'work_file_id');
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
}
