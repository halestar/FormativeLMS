<?php

namespace App\Livewire\Locations;

use App\Models\Locations\Building;
use App\Models\Locations\BuildingArea;
use App\Models\Locations\Room;
use App\Models\SystemTables\SchoolArea;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BuildingAreaEditor extends Component
{
	public array $breadcrumb;
	public BuildingArea $buildingArea;
	public int $buildAreaId;
	public Building $building;
	public Collection $rooms;
	public Collection $areas;
	public ?Room $viewing = null;

	public string $newRoomName = "";

	#[Validate('required|max:200|min:3')]
	public ?string $name = null;

	#[Validate('required|numeric')]
	public ?int $capacity = null;
	public bool $definingBounds = false;
	
	public function messages(): array
	{
		return [
			'name' => __('errors.rooms.name'),
			'newRoomName' => __('errors.rooms.name'),
			'capacity' => __('errors.rooms.capacity'),
		];
	}

	public function loadArea(): void
	{
		$this->buildingArea = BuildingArea::find($this->buildAreaId);
		$this->building = $this->buildingArea->building;
		$this->rooms = $this->buildingArea->rooms;
		$this->areas = $this->building->buildingAreas;
		$this->viewing = null;
		$this->name = null;
		$this->capacity = null;
		$this->definingBounds = false;
		$this->js("window.mapDrawings.loadBuildingArea(" . $this->buildAreaId . ")");
	}
	
	public function mount(BuildingArea $area)
	{
		$this->authorize('has-permission', 'locations.areas');
		$this->buildAreaId = $area->id;
		$this->buildingArea = BuildingArea::find($this->buildAreaId);
		$this->building = $this->buildingArea->building;
		$this->rooms = $this->buildingArea->rooms;
		$this->areas = $this->building->buildingAreas;
		$this->breadcrumb =
			[
				__('system.menu.rooms') => route('locations.buildings.index'),
				$area->building->name => route('locations.buildings.show', $area->building),
				__('locations.buildings.edit') => route('locations.buildings.edit', $area->building),
				__('locations.buildings.maps') => "#",
			];
	}

	public function deleteRoom(Room $room): void
	{
		if($room->canDelete())
			$room->delete();
		$this->removeViewing();
		$this->rooms = $this->buildingArea->rooms;
		$this->js('window.mapDrawings.reload()');
	}

	public function addRoom(): void
	{
		$this->validate(['newRoomName' => 'required|max:200|min:3'], messages: $this->messages());
		$room = new Room();
		$room->name = $this->newRoomName;
		$room->area_id = $this->buildingArea->id;
		$room->save();
		$room->refresh();
		$this->rooms = $this->buildingArea->rooms;
		$this->newRoomName = "";
		$this->viewRoom($room);
	}

	public function viewRoom(Room $room): void
	{
		//check that we can see this room
		if($this->building->rooms()->where('rooms.id', $room->id)->exists())
		{
			$this->viewing = $room;
			$this->name = $this->viewing->name;
			$this->capacity = $this->viewing->capacity;
			$this->js("window.mapDrawings.highlightRoom(" . $room->id . ")");
		}
	}
	
	public function removeViewing(): void
	{
		$this->viewing = null;
		$this->name = null;
		$this->capacity = null;
		$this->definingBounds = false;
		$this->js('window.mapDrawings.removeHighlight()');
	}
	
	public function updateRoom()
	{
		if($this->viewing)
		{
			$data = $this->validate(
				[
					'name' => 'required|max:200|min:3',
					'capacity' => 'required|numeric|min:1',
				], messages: $this->messages());
			$this->viewing->fill($data);
			$this->viewing->save();
			$this->rooms = $this->buildingArea->rooms;
			$this->js('window.mapDrawings.reload()');
		}
	}
	
	public function saveBounds(mixed $data)
	{
		if($this->viewing)
		{
			$this->viewing->img_data = $data;
			$this->viewing->save();
			$this->definingBounds = false;
			$this->rooms = $this->buildingArea->rooms;
			$this->js('window.mapDrawings.reload()');
		}
	}

	public function render()
	{
		return view('livewire.locations.building-area-editor')
			->extends('layouts.app', ['breadcrumb' => $this->breadcrumb])
			->section('content');
	}
}
