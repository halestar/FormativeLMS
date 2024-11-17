<?php

namespace App\Models\Locations;

use App\Traits\SinglePhoneable;
use halestar\LaravelDropInCms\Models\Scopes\OrderByNameScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

#[ScopedBy(OrderByNameScope::class)]
class Room extends Model
{
    use HasFactory, SinglePhoneable;
    public $timestamps = true;
    protected $table = "rooms";
    protected $primaryKey = "id";
    public $incrementing = true;
    protected $fillable =
        [
            'name',
            'area_id',
            'capacity',
            'img_data',
        ];

    protected function casts(): array
    {
        return
            [
                'capacity' => 'integer',
                'img_data' => 'array',
            ];
    }

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
}
