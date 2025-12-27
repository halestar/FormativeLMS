<?php

namespace App\Models\Locations;

use App\Casts\People\Portrait;
use App\Enums\WorkStoragesInstances;
use App\Interfaces\Fileable;
use App\Models\People\Person;
use App\Models\Scopes\OrderByNameScope;
use App\Models\SystemTables\SchoolArea;
use App\Models\Utilities\WorkFile;
use App\Traits\HasWorkFiles;
use App\Traits\Phoneable;
use App\Traits\SingleAddressable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[ScopedBy(OrderByNameScope::class)]
class Building extends Model implements Fileable
{
	use HasFactory, Phoneable, SingleAddressable, HasWorkFiles;
	
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "buildings";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'name',
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
		return $this->belongsToMany(SchoolArea::class, 'buildings_areas', 'building_id', 'area_id')
			->using(BuildingArea::class)
			->as('area')
			->withPivot(
				[
					'id', 'blueprint_url', 'img', 'order',
				])
			->withTimestamps();
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
	
	protected function casts(): array
	{
		return
			[
				'img' => Portrait::class,
			];
	}

	public function getWorkStorageKey(): WorkStoragesInstances
	{
		return WorkStoragesInstances::ProfileWork;
	}

	public function shouldBePublic(): bool
	{
		return true;
	}

	public function canAccessFile(Person $person, WorkFile $file): bool
	{
		return true;
	}

	public function hasArea(SchoolArea $area): bool
	{
		return $this->schoolAreas()
		            ->where('area_id', $area->id)
		            ->exists();
	}
}
