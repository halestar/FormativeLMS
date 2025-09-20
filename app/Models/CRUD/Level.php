<?php

namespace App\Models\CRUD;

use App\Models\Locations\Campus;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Level extends CrudItem
{
	
	protected $table = 'crud_levels';
	
	
	public static function getCrudModel(): string
	{
		return Level::class;
	}
	
	public static function getCrudModelName(): string
	{
		return trans_choice('crud.level', 2);
	}
	
	public function canDelete(): bool
	{
		return false;
	}
	
	public function campuses(): MorphToMany
	{
		return $this->morphedByMany(Campus::class, 'leveable', 'leveables');
	}
	
}

