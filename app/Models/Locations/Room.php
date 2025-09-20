<?php

namespace App\Models\Locations;

use App\Models\SubjectMatter\ClassSession;
use App\Traits\SinglePhoneable;
use halestar\LaravelDropInCms\Models\Scopes\OrderByNameScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

#[ScopedBy(OrderByNameScope::class)]
class Room extends Model
{
	use HasFactory, SinglePhoneable;
	
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "rooms";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'name',
			'area_id',
			'capacity',
			'img_data',
		];
	
	public function canDelete(): bool
	{
		return true;
	}
	
	public function buildingArea(): BelongsTo
	{
		return $this->belongsTo(BuildingArea::class, 'area_id');
	}
	
	public function campuses(): BelongsToMany
	{
		return $this->belongsToMany(Campus::class, 'campuses_rooms', 'room_id', 'campus_id')
		            ->using(CampusRoom::class)
		            ->as('info')
		            ->withPivot(
			            [
				            'label', 'classroom',
			            ]);
	}
	
	public function building(): HasOneThrough
	{
		return $this->hasOneThrough(Building::class, BuildingArea::class, 'id', 'id', 'area_id', 'building_id');
	}
	
	public function scopeFreeFloating(Builder $query): void
	{
		$query->whereNull('area_id');
	}
	
	public function isFreeFloating(): bool
	{
		return $this->area_id == null;
	}
	
	public function isPhysical(): bool
	{
		return $this->area_id != null;
	}
	
	public function currentClassSessions(): Collection
	{
		$classSessionRooms = ClassSession::join('terms', 'terms.id', '=', 'class_sessions.term_id')
		                                 ->whereBetweenColumns(DB::raw(date("'Y-m-d'")),
			                                 ['terms.term_start', 'terms.term_end'])
		                                 ->where('class_sessions.room_id', $this->id)
		                                 ->select('class_sessions.*');
		$periodRooms = ClassSession::join('class_sessions_periods', 'class_sessions_periods.session_id', '=',
			'class_sessions.id')
		                           ->join('terms', 'terms.id', '=', 'class_sessions.term_id')
		                           ->whereBetweenColumns(DB::raw(date("'Y-m-d'")),
			                           ['terms.term_start', 'terms.term_end'])
		                           ->where('class_sessions_periods.room_id', $this->id)
		                           ->select('class_sessions.*');
		return $classSessionRooms->union($periodRooms)
		                         ->get();
	}
	
	protected function casts(): array
	{
		return
			[
				'capacity' => 'integer',
				'img_data' => 'array',
			];
	}
}
