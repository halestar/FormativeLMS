<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Fileable
{
	/**
	 * @return MorphToMany|BelongsToMany The relationships between this model and the work file models.
	 */
	public function workFiles(): MorphToMany|BelongsToMany;
	
	/**
	 * @return string The key used to store the work files in the work storage.
	 */
	public function getWorkStorageKey(): string;
	
	/**
	 * @return bool Whether or not this file should be publicly accessible.
	 */
	public function shouldBePublic(): bool;
}
