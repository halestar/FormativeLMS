<div>
    <div class="input-group mb-3">
        <label for="building" class="input-group-text">Add all rooms in the building</label>
        <select id="building" class="form-select" wire:model="building_id" wire:change="updateBuilding()">
            @foreach(\App\Models\Locations\Building::all() as $building)
                <option value="{{ $building->id }}">{{ $building->name }}</option>
            @endforeach
        </select>
        <button
            type="button"
            wire:click="addBuilding()"
            class="btn btn-primary"
        >Add</button>
    </div>
    <div class="input-group mb-3">
        <label for="area" class="input-group-text">Using the room above, add all rooms in the area </label>
        <select id="area" class="form-select" wire:model="area_id" wire:change="updateBuildingArea()">
            @foreach($selectedBuilding->buildingAreas as $areas)
                <option value="{{ $areas->id }}">{{ $areas->name }}</option>
            @endforeach
        </select>
        <button
            type="button"
            wire:click="addBuildingArea()"
            class="btn btn-primary"
        >Add</button>
    </div>

    <div class="input-group mb-3">
        <label for="room" class="input-group-text">Add the room located in the area above</label>
        <select id="room" class="form-select" wire:model="room_id">
            @foreach($selectedArea->rooms as $room)
                <option value="{{ $room->id }}">{{ $room->name }}</option>
            @endforeach
        </select>
        <button
            type="button"
            wire:click="addRoom()"
            class="btn btn-primary"
        >Add</button>
    </div>


    @foreach($buildings as $building)
        <div class="mb-3" wire:key="{{ $building->id }}">
            <h3 class="border-bottom d-flex justify-content-between align-items-end">
                {{ $building->name }}
                <button
                    type="button"
                    class="btn btn-danger btn-sm"
                    wire:click="removeBuilding({{ $building->id }})"
                ><i class="fa-solid fa-times"></i></button>
            </h3>
            @foreach($buildingAreas->where('building_id', $building->id) as $area)
                <div class="ms-3 mb-3" wire:key="{{ $area->id }}">
                    <h4 class="border-bottom d-flex justify-content-between align-items-end">
                        {{ $area->name }}
                        <button
                            type="button"
                            class="btn btn-danger btn-sm"
                            wire:click="removeBuildingArea({{ $area->id }})"
                        ><i class="fa-solid fa-times"></i></button>
                    </h4>
                    <ul class="list-group list-group-flush">
                        @foreach($rooms->where('area_id', $area->id) as $room)
                            <li class="list-group-item list-group-item-action ms-3 mb-1 pb-0" wire:key="{{ $room->id }}">
                                <div class="row align-items-center">
                                    <label class="col-4">
                                        {{ $room->name }}
                                    </label>
                                    <div class="form-check col-3">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            wire:click="toggleClassroom({{ $room->id }}, {{ $room->info->classroom ? 0 : 1 }})"
                                            id="classroom_{{ $room->id }}"
                                            @if($room->info->classroom)checked @endif
                                        >
                                        <label class="form-check-label" for="classroom_{{ $room->id }}">
                                            Classroom
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-floating ">
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="label_{{ $room->id }}"
                                                placeholder="{{ __('locations.rooms.label') }}"
                                                value="{{ $room->info->label }}"
                                                wire:change="updateLabel({{ $room->id }}, $event.target.value)"
                                            />
                                            <label for="label_{{ $room->id }}">{{ __('locations.rooms.label') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-1 ms-auto">
                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm"
                                            wire:click="removeRoom({{ $room->id }})"
                                        ><i class="fa-solid fa-times"></i></button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
