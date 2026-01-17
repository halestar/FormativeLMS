<div class="container">
    <div class="border-bottom d-flex justify-content-between align-items-baseline mb-3">
        <h2 class="flex-grow-1 text-nowrap">{{ $buildingArea->building->name }}</h2>

        <div class="input-group w-auto">
            <span class="input-group-text">{{ trans_choice('locations.buildings.areas', 1) }}</span>
            <select wire:model="buildAreaId" class="form-select" wire:change="loadArea">
                @foreach($areas as $area)
                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-3">
            <div class="input-group mb-3">
                <span class="input-group-text">{{ __('locations.rooms.add') }}</span>
                <input
                    type="text"
                    wire:model="newRoomName"
                    class="form-control"
                    wire:keydown.enter="addRoom()"
                />
                <button type="button"
                        class="btn btn-primary"
                        wire:click="addRoom()"
                ><i class="fa-solid fa-plus"></i></button>
            </div>
            <ul class="list-group" style="max-height: 60vh; overflow-y: auto;">
                @foreach($rooms as $room)
                    <li
                        class="list-group-item list-group-item-dark list-group-item-action d-flex justify-content-between align-items-center show-as-action @if($viewing && $room->id == $viewing->id) active @endif"
                        wire:key="{{ $room->id }}"
                        wire:click="viewRoom({{ $room->id }})"
                    >
                        {{ $room->name }}
                        @if($room->canDelete())
                            <button
                                    type="button"
                                    class="btn btn-danger btn-sm"
                                    wire:confirm="{{ __('locations.rooms.delete.confirm') }}"
                                    wire:click.stop="deleteRoom({{ $room->id }})"
                            ><i class="fa-solid fa-times"></i></button>
                        @endif
                    </li>

                @endforeach
            </ul>
        </div>
        <div class="col-6">
            <div
                id="blueprint-container" wire:ignore
                x-data
                x-init="window.mapDrawings = new MapDrawings('blueprint-container', {{ $buildingArea->id }}, { action: $wire.viewRoom });"
            >

            </div>
        </div>
        <div class="col-3">
            @if($viewing)
                <div
                    class="border rounded p-3 text-bg-secondary"
                    x-data="
                    {
                        definingBounds: $wire.entangle('definingBounds') ,
                        startDrawing()
                        {
                            this.definingBounds = true;
                            window.mapDrawings.beginDrawing('{{ $viewing->name }}');

                        },
                        clearDrawing()
                        {
                            window.mapDrawings.clearDrawing();
                        },
                        endDrawing()
                        {
                            this.definingBounds = false;
                            window.mapDrawings.endDrawing();
                        }
                    }"
                >
                    <div class="form-floating mb-3">
                        <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                wire:model="name"
                                placeholder="{{ __('locations.rooms.name') }}"
                                value="{{ $viewing->name }}"
                                wire:change="updateRoom"
                        />
                        <label for="name">{{ __('locations.rooms.name') }}</label>
                        <x-utilities.error-display key="name">{{ $errors->first('name') }}</x-utilities.error-display>
                    </div>
                    <div class="form-floating mb-3">
                        <input
                                type="number"
                                min="0"
                                max="100000"
                                class="form-control @error('capacity') is-invalid @enderror"
                                id="capacity"
                                name="capacity"
                                wire:model="capacity"
                                placeholder="{{ __('locations.rooms.capacity') }}"
                                value="{{ $viewing->capacity }}"
                                wire:change="updateRoom"
                        />
                        <label for="name">{{ __('locations.rooms.capacity') }}</label>
                        <x-utilities.error-display key="capacity">{{ $errors->first('capacity') }}</x-utilities.error-display>
                    </div>
                    <livewire:phone-editor :phoneable="$viewing" wire:key="{{ $viewing->id }}"/>

                    <button
                            type="button"
                            class="mt-3 btn @if($definingBounds) btn-danger @else btn-primary @endif w-100"
                            id="drawing-control"
                            @click="startDrawing()"
                            x-cloak
                            x-show="!definingBounds"
                    >{{ __('locations.areas.bounds.define') }}</button>
                    <button
                            type="button"
                            class="mt-3 btn @if($definingBounds) btn-danger @else btn-primary @endif w-100"
                            id="drawing-control"
                            @click="endDrawing()"
                            x-cloak
                            x-show="definingBounds"
                    >{{ __('locations.areas.bounds.defining') }}</button>
                    <button
                            type="button"
                            class="mt-3 btn btn-success w-100"
                            wire:click="saveBounds(window.mapDrawings.getDrawingBounds())"
                            x-cloak
                            x-show="definingBounds"
                    >{{ __('locations.areas.bounds.save') }}</button>
                    <button
                            type="button"
                            class="mt-3 btn btn-warning w-100"
                            @click="clearDrawing()"
                            x-cloak
                            x-show="definingBounds"
                    >{{ __('locations.areas.bounds.clear') }}</button>
                    <button
                            type="button"
                            class="mt-3 btn btn-secondary w-100"
                            wire:click="removeViewing()"
                    >{{ __('common.cancel') }}</button>
                </div>
            @endif
        </div>
    </div>
</div>
@script
<script>
    $wire.on('begin-bounds', () => {
        window.areaEditor.beginDrawing();
    });

    $wire.on('end-bounds', () => {
        window.areaEditor.clear();
        window.areaEditor.endDrawing();
        window.mapDrawings.removeHighlight();
    });
</script>
@endscript
