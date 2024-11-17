<?php

namespace App\Models\Locations;

use App\Models\CRUD\Level;
use App\Models\Scopes\OrderByOrderScope;
use App\Traits\Addressable;
use App\Traits\Phoneable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

#[ScopedBy(OrderByOrderScope::class)]
class Campus extends Model
{
    use Phoneable, Addressable;
    public $timestamps = true;
    protected $table = "campuses";
    protected $primaryKey = "id";
    public $incrementing = true;
    protected $fillable =
        [
            'name',
            'abbr',
            'title',
            'established',
            'order',
            'img',
            'color_pri',
            'color_sec',
        ];

    protected function casts(): array
    {
        return
            [
                'established' => 'date: Y',
            ];
    }

    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(Level::class, 'campuses_levels', 'campus_id', 'level_id');
    }


    public function img(): Attribute
    {
        return Attribute::make
        (
            get: fn(?string $img) => $img?? asset('images/campus_img_placeholder.png'),
        );
    }

    public function canDelete(): bool
    {
        return true;
    }

    public function canRemoveLevel(Level|int $level): bool
    {
        return true;
    }

    public function terms():HasMany
    {
        return $this->hasMany(Term::class, 'campus_id');
    }

    public function yearTerms(Year $year): HasMany
    {
        return $this->terms()->where('year_id', $year->id);
    }

    public function years(): BelongsToMany
    {
        return $this->belongsToMany(Year::class, 'campuses_years', 'campus_id', 'year_id')
            ->groupBy('years.id');
    }

    public function iconHtml($size = "normal", $css = null): string
    {
        return '<div class="border rounded p-2 icon-container ' . $css . '" style="background-color:' .
                $this->color_pri . ';"><div class="campus-icon-' . $size .
                '" style="color: ' . $this->color_sec . ';">' . $this->icon . '</div></div>';
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'campuses_rooms', 'campus_id', 'room_id')
            ->using(CampusRoom::class)
            ->as('info')
            ->withPivot(
                [
                    'label', 'classroom',
                ]);
    }

    public function buildings(): Collection
    {
        return Building::select('buildings.*')
            ->join('buildings_areas', 'buildings.id', '=', 'buildings_areas.building_id')
            ->join('rooms', 'rooms.area_id', '=', 'buildings_areas.id')
            ->join('campuses_rooms', 'rooms.id', '=', 'campuses_rooms.room_id')
            ->where('campuses_rooms.campus_id', $this->id)
            ->groupBy('buildings.id')
            ->get();
    }

    public function buildingAreas(Building $building = null): Collection
    {
        $query = BuildingArea::select('buildings_areas.*')
            ->join('rooms', 'rooms.area_id', '=', 'buildings_areas.id')
            ->join('campuses_rooms', 'rooms.id', '=', 'campuses_rooms.room_id')
            ->where('campuses_rooms.campus_id', $this->id);
        if($building)
            $query->where('buildings_areas.building_id', $building->id);
        $query->groupBy('buildings_areas.id');
        return $query->get();
    }
}
