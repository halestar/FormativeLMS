<div>
    <h4 class="border-bottom pb-1 mb-2">{{ __('crud.now_editing') }} {{ $crudModel::getCrudModelName() }}</h4>

    <div class="d-flex justify-content-between align-items-center pb-2">
        <button type="button" class="btn btn-primary btn-sm" wire:click="newEntry()">{{ __('crud.add') }}</button>
        <div>
            {{ __('crud.autosort') }}
            <button class="btn btn-secondary btn-sm p-1 m-1" wire:click="sort()"><i class="fa-solid fa-arrow-up-a-z"></i></button>
            <button class="btn btn-secondary btn-sm p-1 m-1" wire:click="sort(false)"><i class="fa-solid fa-arrow-down-z-a"></i></button>
        </div>
    </div>

    <ul wire:sortable="updateCrudOrder" class="list-group">
        @foreach ($crudItems as $crudItem)
            <li wire:sortable.item="{{ $crudItem->crudKey() }}" wire:key="crud-item-{{ $crudItem->crudKey() }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <div>
                    <span wire:sortable.handle class="show-as-action me-2"><i class="fa-solid fa-grip-lines-vertical"></i></span>
                    <span onclick="$('#crud-name-{{ $crudItem->crudKey() }}').hide();$('#crud-input-{{ $crudItem->crudKey() }}').show();" id="crud-name-{{ $crudItem->crudKey() }}">{{ $crudItem->crudName() }}</span>
                    <input id="crud-input-{{ $crudItem->crudKey() }}" type="text" wire:change="updateName({{ $crudItem->crudKey() }}, $event.target.value)" value="{{ $crudItem->crudName() }}" style="display: none;" />
                </div>
                <button
                    type="button"
                    class="btn btn-danger btn-sm"
                    @if(!$crudItem->canDelete()) disabled @endif
                    wire:confirm="{{ __('crud.remove.confirm') }}"
                    wire:click="deleteEntry({{ $crudItem->crudKey() }})"
                >{{ __('crud.remove') }}</button>
            </li>
        @endforeach
    </ul>
</div>
