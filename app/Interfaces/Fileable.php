<?php

namespace App\Interfaces;

use App\Enums\WorkStoragesInstances;
use App\Models\People\Person;
use App\Models\Utilities\WorkFile;
use App\Observers\FileableObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Fileable
{
	/**
	 * This relationship is defined im the HasWorkFiles trait.
	 * @return MorphToMany The relationships between this model and the work file models.
	 * @see HasWorkFiles
	 */
	public function workFiles(): MorphMany;
	
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

	/**
	 * @param Person $person The person to check access for.
	 * @param WorkFile $file The file to check access for.
	 * @return bool Whether or not the person can access the file.
	 */
	public function canAccessFile(Person $person, WorkFile $file): bool;
}
