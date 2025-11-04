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
	public BuildingArea $buildingArea;
	public SchoolArea $schoolArea;
	public Building $building;
	public Collection $rooms;
	public ?Room $viewing = null;
	#[Validate('required|max:200')]
	public ?string $name;
	#[Validate('required|numeric')]
	public ?int $capacity;
	public bool $definingBounds;
	
	public function messages(): array
	{
		return [
			'name' => __('errors.terms.label'),
			'capacity' => __('errors.terms.campus_id'),
		];
	}
	
	public function mount(BuildingArea $area): void
	{
		$this->buildingArea = $area;
		$this->schoolArea = $area->schoolArea;
		$this->building = $area->building;
		$this->rooms = $area->rooms;
	}
	
	public function removeViewing(): void
	{
		$this->viewing = null;
		$this->name = null;
		$this->capacity = null;
		$this->clearDefineBounds();
	}
	
	public function clearDefineBounds()
	{
		$this->definingBounds = false;
		$this->dispatch('end-bounds');
	}
	
	public function updateRoomName()
	{
		if($this->viewing && $this->name)
		{
			$this->viewing->name = $this->name;
			$this->viewing->save();
			$this->rooms = $this->buildingArea->rooms;
		}
	}
	
	public function updateRoomCapacity()
	{
		if($this->viewing && $this->capacity)
		{
			$this->viewing->capacity = $this->capacity;
			$this->viewing->save();
			$this->rooms = $this->buildingArea->rooms;
		}
	}
	
	public function defineBounds()
	{
		$this->definingBounds = true;
		$this->dispatch('begin-bounds');
	}
	
	public function saveBounds(mixed $data)
	{
		if($this->viewing)
		{
			$this->viewing->img_data = $data;
			$this->viewing->save();
			$this->clearDefineBounds();
		}
	}
	
	public function deleteRoom(Room $room): void
	{
		if($room->canDelete())
			$room->delete();
		$this->rooms = $this->buildingArea->rooms;
	}
	
	public function addRoom(): void
	{
		$room = new Room();
		$room->name = __('locations.rooms.new');
		$room->virtual = false;
		$room->area_id = $this->buildingArea->id;
		$room->save();
		$this->rooms = $this->buildingArea->rooms;
		$this->setViewing($room);
	}
	
	public function setViewing(Room $room): void
	{
		$this->viewing = $room;
		$this->name = $this->viewing->name;
		$this->capacity = $this->viewing->capacity;
	}
	
	public function render()
	{
		return view('livewire.locations.building-area-editor');
	}
}
