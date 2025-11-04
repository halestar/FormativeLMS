<?php

namespace App\Traits;

use App\Models\Utilities\WorkFile;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasUuidWorkFiles
{
	public function workFiles(): MorphToMany
	{
		return $this->morphToMany(WorkFile::class, 'fileable', 'uuid_fileables');
	}
}
