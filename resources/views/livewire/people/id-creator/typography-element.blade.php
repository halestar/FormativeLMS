<li class="list-group-item">
    <h5 class="mb-3">{{ __('people.id.typography') }}</h5>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.typography.fs') }}</span>
        <input
                type="number"
                min="0.1"
                step="0.1"
                class="form-control"
                wire:change="updateSetting('font-size', $event.target.value)"
                value="{{ $element->getConfig('font-size', 1) }}"
        />
        <span class="input-group-text">{{ __('common.em') }}</span>
    </div>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.typography.ff') }}</span>
        <select
                class="form-select"
                wire:change="updateSetting('font-family', $event.target.value)"
        >
            @foreach(config('lms.fonts') as $font)
                <option
                        value="{{ $font }}"
                        style="font-family: {{ $font }};"
                        @selected($element->getConfig('font-family', 'Arial') == $font)
                >{{ $font }}</option>
            @endforeach
        </select>
    </div>
    <div class="input-group mb-3">
        <span class="input-group-text">{{ __('people.id.typography.tc') }}</span>
        <input
                type="color"
                class="form-control-color"
                wire:change="updateSetting('color', $event.target.value)"
                value="{{ $element->getConfig('color', "#000000") }}"
        />
    </div>
    <div class="form-check fs-5">
        <input
                type="checkbox"
                class="form-check-input"
                @checked($element->getConfig('italics'))
                wire:change="updateSetting('italics', $event.target.checked)"
        />
        <label class="form-check-label">{{ __('people.id.typography.i') }}</label>
    </div>
    <div class="form-check fs-5">
        <input
                type="checkbox"
                class="form-check-input"
                @checked($element->getConfig('bold'))
                wire:change="updateSetting('bold', $event.target.checked)"
        />
        <label class="form-check-label">{{ __('people.id.typography.b') }}</label>
    </div>
    <div class="form-check fs-5 mb-3">
        <input
                type="checkbox"
                class="form-check-input"
                @checked($element->getConfig('underline'))
                wire:change="updateSetting('underline', $event.target.checked)"
        />
        <label class="form-check-label">{{ __('people.id.typography.u') }}</label>
    </div>
    <div class="btn-group" role="group">
        <input
                type="radio"
                class="btn-check"
                name="text-align"
                id="text-align-start"
                autocomplete="off"
                @checked($element->getConfig('text-align', 'start') == "start")
                wire:change="updateSetting('text-align', 'start')"
                title="{{ __('people.id.typography.align.start') }}"
        />
        <label class="btn btn-outline-dark" for="text-align-start"><i class="fa-solid fa-align-left"></i></label>

        <input
                type="radio"
                class="btn-check"
                name="text-align"
                id="text-align-center"
                autocomplete="off"
                @checked($element->getConfig('text-align', 'start') == "center")
                wire:change="updateSetting('text-align', 'center')"
                title="{{ __('people.id.typography.align.center') }}"
        />
        <label class="btn btn-outline-dark" for="text-align-center"><i class="fa-solid fa-align-center"></i></label>

        <input
                type="radio"
                class="btn-check"
                name="text-align"
                id="text-align-end"
                autocomplete="off"
                @checked($element->getConfig('text-align', 'start') == "end")
                wire:change="updateSetting('text-align', 'end')"
                title="{{ __('people.id.typography.align.end') }}"
        />
        <label class="btn btn-outline-dark" for="text-align-end"><i class="fa-solid fa-align-right"></i></label>
    </div>
</li>
<li class="list-group-item">
    <h5 class="mb-3">{{ __('people.id.text.shadow') }}</h5>
    <div class="form-check fs-5">
        <input
                type="checkbox"
                class="form-check-input"
                @checked($element->getConfig('text-shadow'))
                wire:change="updateSetting('text-shadow', $event.target.checked)"
        />
        <label class="form-check-label">{{ __('people.id.text.shadow') }}</label>
    </div>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.text.shadow.x') }}</span>
        <input
                type="number"
                min="0"
                step="1"
                class="form-control"
                wire:change="updateSetting('text-shadow', $event.target.value)"
                value="{{ $element->getConfig('text-shadow', 1) }}"
        />
        <span class="input-group-text">{{ __('common.px') }}</span>
    </div>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.text.shadow.y') }}</span>
        <input
                type="number"
                min="0"
                step="1"
                class="form-control"
                wire:change="updateSetting('text-shadow-y', $event.target.value)"
                value="{{ $element->getConfig('text-shadow-y', 1) }}"
        />
        <span class="input-group-text">{{ __('common.px') }}</span>
    </div>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.text.shadow.blur') }}</span>
        <input
                type="number"
                min="0"
                step="1"
                class="form-control"
                wire:change="updateSetting('text-shadow-blur', $event.target.value)"
                value="{{ $element->getConfig('text-shadow-blur', 1) }}"
        />
        <span class="input-group-text">{{ __('common.px') }}</span>
    </div>
    <div class="input-group mb-3">
        <span class="input-group-text">{{ __('people.id.text.shadow.color') }}</span>
        <input
                type="color"
                class="form-control-color"
                wire:change="updateSetting('text-shadow-color', $event.target.value)"
                value="{{ $element->getConfig('text-shadow-color', "#000000") }}"
        />
    </div>
</li>
