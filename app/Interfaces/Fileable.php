<?php

namespace App\Interfaces;

use App\Enums\WorkStoragesInstances;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Fileable
{
	/**
	 * This relationship is defined im the HasWorkFiles trait.
	 * @return MorphToMany The relationships between this model and the work file models.
	 * @see HasWorkFiles
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

	/**
	 * This relationship is defined im the HasWorkFiles trait.
	 * @param Fileable $source The location to copy the work files from.
	 * @return void
	 * @see HasWorkFiles
	 */
	public function copyWorkFilesFrom(Fileable $source): void;

	/**
	 * This relationship is defined im the HasWorkFiles trait.
	 * @param Fileable $target The location to copy the work files to.
	 * @return void
	 * @see HasWorkFiles
	 */
	public function copyWorkFilesTo(Fileable $target): void;
}
