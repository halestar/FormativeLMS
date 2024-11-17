<div>

    <div class="input-group mb-3">
        <div class="input-group-text">
            <input
                type="checkbox"
                id="free_floating"
                wire:model="isFree"
                class="form-check-input mt-0"
                wire:click="toggleFreeFloat()"
            />
        </div>
        <label class="input-group-text" for="free_floating">{{ trans_choice('locations.rooms.free', 1) }}</label>
    </div>
    @if(!$isFree)
        <div class="mb-3">
            <label for="building_id" class="form-label">{{ trans_choice('locations.buildings',1) }}</label>
            <select class="form-select" id="building_id" wire:model="building_id" wire:change="updateBuilding()">
                @foreach(\App\Models\Locations\Building::all() as $building)
                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="building_id" class="form-label">{{ trans_choice('locations.buildings.areas',1) }}</label>
            <select class="form-select" id="area_id" wire:model="area_id" wire:change="updateArea()">
                @foreach($buildingAreas as $area)
                    <option value="{{ $area->id }}">{{ $area->schoolArea->name }}</option>
                @endforeach
            </select>
        </div>
   @endif
    <div class="mb-3">
        <label class="form-label" for="notes">{{ __('locations.rooms.notes') }}</label>
        <textarea
            class="form-control"
            id="notes" rows="3"
            wire:model="notes"
            wire:change="updateNotes()"
        >{!! $room->notes !!}</textarea>
    </div>

</div>
