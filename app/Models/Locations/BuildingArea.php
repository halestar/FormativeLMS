<?php

namespace App\Models\Locations;

use App\Casts\People\Portrait;
use App\Models\SystemTables\SchoolArea;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BuildingArea extends Pivot
{
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "buildings_areas";
	protected $primaryKey = "id";
	
	public function canDelete(): bool
	{
		return true;
	}
	
	public function building(): BelongsTo
	{
		return $this->belongsTo(Building::class, 'building_id');
	}
	
	public function schoolArea(): BelongsTo
	{
		return $this->belongsTo(SchoolArea::class, 'area_id');
	}
	
	public function rooms(): HasMany
	{
		return $this->hasMany(Room::class, 'area_id');
	}
	
	public function name(): Attribute
	{
		return Attribute::make
		(
			get: fn() => $this->schoolArea->name
		);
	}
	
	protected function casts(): array
	{
		return
			[
				'blueprint_url' => Portrait::class,
			];
	}
}
