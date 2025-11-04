<?php

namespace App\Models\Locations;

use App\Models\SystemTables\SchoolArea;
use App\Traits\Phoneable;
use App\Traits\SingleAddressable;
use halestar\LaravelDropInCms\Models\Scopes\OrderByNameScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[ScopedBy(OrderByNameScope::class)]
class Building extends Model
{
	use HasFactory, Phoneable, SingleAddressable;
	
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "buildings";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'name',
			'img',
		];
	
	public function canDelete(): bool
	{
		return ($this->buildingAreas()
		             ->count() == 0);
	}
	
	public function buildingAreas(): HasMany
	{
		return $this->hasMany(BuildingArea::class, 'building_id');
	}
	
	public function schoolAreas(): BelongsToMany
	{
		return $this->belongsToMany(SchoolArea::class, 'buildings_areas', 'building_id', 'area_id');
	}
	
	public function canRemoveArea(SchoolArea $area): bool
	{
		$buildingArea = $this->buildingAreas()
		                     ->where('area_id', $area->id)
		                     ->first();
		if(!$buildingArea)
			return true;
		if($buildingArea->rooms()
		                ->count() > 0)
			return false;
		return true;
	}
	
	public function rooms(): HasManyThrough
	{
		return $this->hasManyThrough(Room::class, BuildingArea::class, 'building_id', 'area_id');
	}
	
	public function img(): Attribute
	{
		return Attribute::make
		(
			get: fn(?string $img) => $img ?? asset('images/campus_img_placeholder.png'),
		);
	}
	
	protected function casts(): array
	{
		return
			[
				'name' => 'string',
			];
	}
}
