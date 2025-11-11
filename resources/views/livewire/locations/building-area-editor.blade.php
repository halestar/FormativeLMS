<div class="container">
    <div class="border-bottom d-flex justify-content-between align-items-baseline mb-3 @if(count($errors)) d-none @endif"
         id="add-header">
        <h2>{{ $buildingArea->building->name }}: {{ $buildingArea->schoolArea->name }}</h2>

        <div>
            <button
                    class="btn btn-primary"
                    type="button"
                    wire:click="addRoom()"
            ><i class="fa-solid fa-plus border-end pe-2 me-2"></i>{{ __('locations.rooms.add') }}</button>
        </div>
    </div>

    <div class="row">
        <div class="col-3">
            <ul class="list-group">
                @foreach($rooms as $room)
                    <li
                            class="list-group-item list-group-item-dark list-group-item-action d-flex justify-content-between align-items-center"
                            wire:key="{{ $room->id }}"
                            wire:click="setViewing({{ $room->id }})"
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
            <div id="blueprint-container" wire:ignore></div>
        </div>
        <div class="col-3">
            @if($viewing)
                <div class="border rounded p-3 text-bg-secondary">
                    <div class="form-floating mb-3">
                        <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                wire:model="name"
                                placeholder="{{ __('locations.rooms.name') }}"
                                value="{{ $viewing->name }}"
                                wire:change="updateRoomName"
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
                                wire:change="updateRoomCapacity"
                        />
                        <label for="name">{{ __('locations.rooms.capacity') }}</label>
                        <x-utilities.error-display key="capacity">{{ $errors->first('capacity') }}</x-utilities.error-display>
                    </div>
                    <livewire:phone-editor :phoneable="$viewing" wire:key="{{ $viewing->id }}"/>
                    <button
                            type="button"
                            class="mt-3 btn @if($definingBounds) btn-danger @else btn-primary @endif w-100"
                            id="drawing-control"
                            @if($definingBounds)
                                wire:click="clearDefineBounds()"
                            @else
                                wire:click="defineBounds()"
                            @endif
                    >{{ $definingBounds? __('locations.areas.bounds.defining'): __('locations.areas.bounds.define') }}</button>
                    @if($definingBounds)
                        <button
                                type="button"
                                class="mt-3 btn btn-success w-100"
                                wire:click="saveBounds(areaEditor.getData())"
                        >{{ __('locations.areas.bounds.save') }}</button>
                        <button
                                type="button"
                                class="mt-3 btn btn-warning w-100"
                                onclick="window.areaEditor.clear()"
                        >{{ __('locations.areas.bounds.clear') }}</button>
                    @endif
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
    $(document).ready(function () {
        window.mapDrawings = new MapDrawings('blueprint-container', {{ $buildingArea->id }});
        window.areaEditor = new AreaDrawing(mapDrawings.getCanvas(), mapDrawings.getCtx());
    });
    $wire.on('begin-bounds', () => {
        window.areaEditor.beginDrawing();
    });

    $wire.on('end-bounds', () => {
        window.areaEditor.clear();
        window.areaEditor.endDrawing();
        window.mapDrawings.loadBuildingArea();
    });
</script>
@endscript
