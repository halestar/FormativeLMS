<?php

namespace App\Models\SystemTables;

use App\Models\Locations\Building;
use App\Models\Locations\BuildingArea;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolArea extends SystemTableTemplate
{
	
	public static function getCrudModelName(): string
	{
		return trans_choice('locations.buildings.areas', 2);
	}
	
	public function canDelete(): bool
	{
		return false;
	}
	
	public function buildingAreas(): HasMany
	{
		return $this->hasMany(BuildingArea::class, 'area_id');
	}
	
	public function buildings(): BelongsToMany
	{
		return $this->belongsToMany(Building::class, 'building_areas', 'area_id', 'building_id');
	}
}

