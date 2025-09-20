<?php

namespace App\Livewire\Locations;

use App\Models\Locations\Building;
use App\Models\Locations\BuildingArea;
use App\Models\Locations\Campus;
use App\Models\Locations\Room;
use Illuminate\Support\Collection;
use Livewire\Component;

class CampusRoomAssigner extends Component
{
	public Campus $campus;
	public Collection $buildings;
	public Collection $buildingAreas;
	public Collection $rooms;
	public int $building_id;
	public int $area_id;
	public int $room_id;
	public Building $selectedBuilding;
	public BuildingArea $selectedArea;
	
	public function mount(Campus $campus): void
	{
		$this->campus = $campus;
		$this->selectedBuilding = Building::first();
		$this->building_id = $this->selectedBuilding->id;
		$this->selectedArea = $this->selectedBuilding->buildingAreas->first();
		$this->area_id = $this->selectedArea->id;
		$this->room_id = $this->selectedArea->rooms()
		                                    ->first()->id;
		$this->updateContent();
	}
	
	private function updateContent(): void
	{
		$this->buildings = $this->campus->buildings();
		$this->buildingAreas = $this->campus->buildingAreas();
		$this->rooms = $this->campus->rooms;
	}
	
	public function addRoom(): void
	{
		$this->campus->rooms()
		             ->syncWithoutDetaching([$this->room_id]);
		$this->updateContent();
	}
	
	public function removeRoom(Room $room): void
	{
		$this->campus->rooms()
		             ->detach($room);
		$this->updateContent();
	}
	
	public function addBuildingArea()
	{
		$this->campus->rooms()
		             ->syncWithoutDetaching($this->selectedArea->rooms->pluck('id')
		                                                              ->toArray());
		$this->updateContent();
	}
	
	public function updateBuildingArea(): void
	{
		$this->selectedArea = BuildingArea::find($this->area_id);
		$this->room_id = $this->selectedArea->rooms()
		                                    ->first()->id;
		$this->updateContent();
	}
	
	public function removeBuildingArea(BuildingArea $area): void
	{
		$this->campus->rooms()
		             ->detach($this->rooms->where('area_id', $area->id)
		                                  ->pluck('id')
		                                  ->toArray());
		$this->updateContent();
	}
	
	public function addBuilding(): void
	{
		$this->campus->rooms()
		             ->syncWithoutDetaching($this->selectedBuilding->rooms->pluck('id')
		                                                                  ->toArray());
		$this->updateContent();
	}
	
	public function updateBuilding(): void
	{
		$this->selectedBuilding = Building::find($this->building_id);
		$this->selectedArea = $this->selectedBuilding->buildingAreas->first();
		$this->area_id = $this->selectedArea->id;
		$this->room_id = $this->selectedArea->rooms()
		                                    ->first()->id;
		$this->updateContent();
	}
	
	public function removeBuilding(Building $building): void
	{
		$this->campus->rooms()
		             ->detach($building->rooms->pluck('id')
		                                      ->toArray());
		$this->updateContent();
	}
	
	public function toggleClassroom($room_id, $isClassroom)
	{
		$this->campus->rooms()
		             ->updateExistingPivot($room_id, ['classroom' => $isClassroom]);
		$this->updateContent();
	}
	
	public function updateLabel($room_id, $label)
	{
		$this->campus->rooms()
		             ->updateExistingPivot($room_id, ['label' => $label]);
		$this->updateContent();
	}
	
	public function render()
	{
		return view('livewire.locations.campus-room-assigner');
	}
}
