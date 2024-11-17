<?php

namespace App\Livewire\Locations;

use App\Models\Locations\Building;
use App\Models\Locations\BuildingArea;
use App\Models\Locations\Room;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class RoomTypeEditor extends Component
{
    public Room $room;
    public bool $isFree;

    public ?string $notes;
    public ?int $building_id;
    public ?int $area_id;
    public Collection $buildingAreas;

    public Collection $buildings;

    private function updateType(): void
    {
        if($this->room->building)
            $this->building_id = $this->room->building->id;
        elseif($this->buildings->count() > 0)
            $this->building_id = $this->buildings->first()->id;
        else
            $this->building_id = null;

        $this->area_id = $this->room->area_id;
        if($this->building_id)
            $this->buildingAreas = BuildingArea::where('building_id', $this->building_id)->get();
        else
            $this->buildingAreas = new Collection();

        if(!$this->area_id)
            $this->area_id = $this->buildingAreas->count() > 0? $this->buildingAreas->first()->id : null;
    }

    public function mount(Room $room): void
    {
        $this->room = $room;
        $this->buildings = Building::all();
        $this->isFree = $this->room->isFreeFloating();
        $this->notes = $this->room->notes;
        $this->updateType();
    }

    public function toggleFreeFloat(): void
    {
        if($this->isFree)
        {
            $this->room->area_id = null;
            $this->room->save();
            $this->updateType();
        }
        else
        {
            $this->updateType();
            $this->room->area_id = $this->area_id;
            $this->room->save();
        }
    }

    public function updateArea(): void
    {
        $this->room->area_id = $this->area_id;
        $this->room->save();
    }

    public function updateNotes(): void
    {
        $this->room->notes = $this->notes;
        $this->room->save();
    }

    public function updateBuilding(): void
    {
        $this->buildingAreas = BuildingArea::where('building_id', $this->building_id)->get();
        $this->area_id = $this->buildingAreas->count() > 0? $this->buildingAreas->first()->id : null;
        $this->updateArea();
    }


    public function render(): Factory|Application|\Illuminate\Contracts\View\View|View
    {
        return view('livewire.locations.room-type-editor');
    }
}
