<?php

namespace App\Models\Locations;

use App\Models\CRUD\SchoolArea;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuildingArea extends Model
{
    public $timestamps = true;
    protected $table = "buildings_areas";
    protected $primaryKey = "id";
    public $incrementing = true;
    protected $fillable =
        [
            'name',
            'short',
            'building_id',
            'virtual',
            'url',
            'capacity',
        ];

    protected function casts(): array
    {
        return
            [
                'virtual' => 'boolean',
                'capacity' => 'integer',
            ];
    }

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
}
