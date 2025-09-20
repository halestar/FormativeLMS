<div class="form-check form-switch {{ $classes }}">
    <input
            class="form-check-input mt-1"
            type="checkbox"
            role="switch"
            id="{{ $elId }}"
            name="{{ $elId }}"
            wire:model.live="state"
            switch
            wire:change="toggle()"
            @if($confirm)
                wire:confirm="{{ $confirm }}"
            @endif
            @checked($state)
            @if($onChange)
                @click="{!! $onChange !!}"
            @endif
    />
    @if($label)
        <label class="form-check-label" for="{{ $elId }}">{{ $label }}</label>
    @endif
</div>
