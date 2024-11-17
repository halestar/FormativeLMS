<?php

namespace App\Models\CRUD;

use App\Models\Locations\Building;
use App\Models\Locations\BuildingArea;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolArea extends CrudItem
{

    protected $table = 'crud_school_areas';


    public static function getCrudModel(): string
    {
        return SchoolArea::class;
    }

    public function canDelete(): bool
    {
        return false;
    }

    public static function getCrudModelName(): string
    {
        return trans_choice('locations.buildings.areas', 2);
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

