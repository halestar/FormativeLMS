<?php

namespace App\Interfaces;

use App\Enums\WorkStoragesInstances;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Fileable
{
	/**
	 * @return MorphToMany|BelongsToMany The relationships between this model and the work file models.
	 */
	public function workFiles(): MorphToMany|BelongsToMany;
	
	/**
	 * @return WorkStoragesInstances The type of work storage that this filable uses.
	 */
	public function getWorkStorageKey(): WorkStoragesInstances;
	
	/**
	 * @return bool Whether or not this file should be publicly accessible.
	 */
	public function shouldBePublic(): bool;
}
