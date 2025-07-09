<li class="list-group-item">
    <h5 class="mb-3">{{ __('people.id.barcode.settings') }}</h5>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.barcode.type') }}</span>
        <select
            class="form-select"
            wire:change="updateSetting('barcode-type', $event.target.value)"
        >
            @foreach(\App\Classes\IdCard\BarcodeGenerator::BARCODE_TYPES as $type)
                <option value="{{ $type }}" @selected($element->getConfig('barcode-type', 'code-128') == $type)>{{ $type }}</option>
            @endforeach
        </select>
    </div>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.barcode.scale.x') }}</span>
        <input
            type="number"
            min=".1"
            step=".1"
            class="form-control"
            wire:change="updateSetting('barcode-scale-x', $event.target.value)"
            value="{{ $element->getConfig('barcode-scale-x', 1) }}"
        />
    </div>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.barcode.scale.y') }}</span>
        <input
            type="number"
            min=".1"
            step=".1"
            class="form-control"
            wire:change="updateSetting('barcode-scale-y', $event.target.value)"
            value="{{ $element->getConfig('barcode-scale-y', 1) }}"
        />
    </div>
</li>
<li class="list-group-item">
    <h5 class="mb-3">{{ __('people.id.barcode.color') }}</h5>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.barcode.padding') }}</span>
        <input
            type="number"
            min="0"
            step="1"
            class="form-control"
            wire:change="updateSetting('barcode-padding', $event.target.value)"
            value="{{ $element->getConfig('barcode-padding', 20) }}"
        />
    </div>
    <div class="input-group mb-1">
        <div class="input-group-text">
            <input
                class="form-check-input mt-0"
                type="checkbox"
                @checked(!$element->getConfig('barcode-transparent-bg', true))
                wire:click="updateSetting('barcode-transparent-bg', !$event.target.checked)"
            >
        </div>
        <span class="input-group-text">{{ __('people.id.barcode.color.background') }}</span>
        <input
            type="color"
            class="form-control-color"
            wire:change="updateSetting('barcode-bg-color', $event.target.value)"
            value="{{ $element->getConfig('barcode-bg-color', "#ffffff") }}"
        />
    </div>
    <div class="input-group mb-1">
        <div class="input-group-text">
            <input
                class="form-check-input mt-0"
                type="checkbox"
                @checked(!$element->getConfig('barcode-transparent-space', true))
                wire:click="updateSetting('barcode-transparent-space', !$event.target.checked)"
            >
        </div>
        <span class="input-group-text">{{ __('people.id.barcode.color.space') }}</span>
        <input
            type="color"
            class="form-control-color"
            wire:change="updateSetting('barcode-space-color', $event.target.value)"
            value="{{ $element->getConfig('barcode-space-color', "#ffffff") }}"
        />
    </div>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.barcode.color.bar') }}</span>
        <input
            type="color"
            class="form-control-color"
            wire:change="updateSetting('barcode-bar-color', $event.target.value)"
            value="{{ $element->getConfig('barcode-bar-color', "#000000") }}"
        />
    </div>
</li>
<li class="list-group-item">
    <h5 class="mb-3">{{ __('people.id.barcode.text') }}</h5>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.barcode.fs') }}</span>
        <input
            type="number"
            min="1"
            step="1"
            class="form-control"
            wire:change="updateSetting('barcode-text-size', $event.target.value)"
            value="{{ $element->getConfig('barcode-text-size', 10) }}"
        />
        <span class="input-group-text">pt</span>
    </div>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.barcode.ff') }}</span>
        <select
            class="form-select"
            wire:change="updateSetting('barcode-text-font', $event.target.value)"
        >
            @foreach(config('lms.fonts') as $font)
                <option
                    value="{{ $font }}"
                    style="font-family: {{ $font }};"
                    @selected($element->getConfig('barcode-text-font', 'monospace') == $font)
                >{{ $font }}</option>
            @endforeach
        </select>
    </div>
    <div class="input-group mb-3">
        <span class="input-group-text">{{ __('people.id.barcode.fc') }}</span>
        <input
            type="color"
            class="form-control-color"
            wire:change="updateSetting('barcode-text-color', $event.target.value)"
            value="{{ $element->getConfig('barcode-text-color', "#000000") }}"
        />
    </div>
</li>
