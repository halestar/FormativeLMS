<li class="list-group-item" x-data="{ underline_selected: {{$element->getConfig('border', false)? 'true': 'false'}} }">
    <h5 class="mb-3">{{ __('people.id.image') }}</h5>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.image.padding') }}</span>
        <input
                type="number"
                min="0"
                step="1"
                class="form-control"
                wire:change="updateSetting('img-padding', $event.target.value)"
                value="{{ $element->getConfig('img-padding', 0) }}"
        />
        <span class="input-group-text">{{ __('common.px') }}</span>
    </div>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.image.margin') }}</span>
        <input
                type="number"
                min="0"
                step="1"
                class="form-control"
                wire:change="updateSetting('img-margin', $event.target.value)"
                value="{{ $element->getConfig('img-margin', 0) }}"
        />
        <span class="input-group-text">{{ __('common.px') }}</span>
    </div>
    <div class="input-group mb-1">
        <span class="input-group-text">Rounded Corners</span>
        <input
                type="number"
                min="0"
                step="1"
                max="100"
                class="form-control"
                wire:change="updateSetting('border-radius', $event.target.value)"
                value="{{ $element->getConfig('border-radius', 0) }}"
        />
        <span class="input-group-text">%</span>
    </div>
    <div class="form-check fs-5 mb-3">
        <input
                type="checkbox"
                class="form-check-input"
                @checked($element->getConfig('border', false))
                wire:change="updateSetting('border', $event.target.checked)"
                x-on:change="underline_selected = !underline_selected"
                id="image-border"
        />
        <label class="form-check-label" for="image-border">Image Border</label>
    </div>
    <div class="ms-2 mb-3" x-show="underline_selected">
        <div class="input-group mb-1">
            <span class="input-group-text">Border Color</span>
            <input
                    type="color"
                    class="form-control-color"
                    wire:change="updateSetting('border-color', $event.target.value)"
                    value="{{ $element->getConfig('border-color', "#000000") }}"
            />
        </div>
        <div class="input-group mb-1">
            <span class="input-group-text">Border Width</span>
            <input
                    type="number"
                    min="1"
                    step="1"
                    class="form-control"
                    wire:change="updateSetting('border-width', $event.target.value)"
                    value="{{ $element->getConfig('border-width', 1) }}"
            />
            <span class="input-group-text">{{ __('common.px') }}</span>
        </div>
        <div class="input-group mb-1">
            <span class="input-group-text">Border Style</span>
            <select
                    class="form-select"
                    wire:change="updateSetting('border-style', $event.target.value)"
            >
                <option value="solid" @selected($element->getConfig('border-style', 'solid') == "solid")>Solid</option>
                <option value="dashed" @selected($element->getConfig('border-style', 'solid') == "dashed")>Dashed
                </option>
                <option value="dotted" @selected($element->getConfig('border-style', 'solid') == "dotted")>Dotted
                </option>
                <option value="double" @selected($element->getConfig('border-style', 'solid') == "double")>Double
                </option>
                <option value="groove" @selected($element->getConfig('border-style', 'solid') == "groove")>Groove
                </option>
                <option value="ridge" @selected($element->getConfig('border-style', 'solid') == "ridge")>Ridge</option>
                <option value="inset" @selected($element->getConfig('border-style', 'solid') == "inset")>Inset</option>
                <option value="outset" @selected($element->getConfig('border-style', 'solid') == "outset")>Outset
                </option>
            </select>
        </div>
    </div>
</li>
<li class="list-group-item">
    <h5 class="mb-3">{{ __('people.id.image.shadow') }}</h5>
    <div class="form-check fs-5">
        <input
                type="checkbox"
                class="form-check-input"
                @checked($element->getConfig('img-shadow'))
                wire:change="updateSetting('img-shadow', $event.target.checked)"
        />
        <label class="form-check-label">{{ __('people.id.image.shadow') }}</label>
    </div>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.image.shadow.x') }}</span>
        <input
                type="number"
                min="0"
                step="1"
                class="form-control"
                wire:change="updateSetting('img-shadow', $event.target.value)"
                value="{{ $element->getConfig('img-shadow', 1) }}"
        />
        <span class="input-group-text">{{ __('common.px') }}</span>
    </div>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.image.shadow.y') }}</span>
        <input
                type="number"
                min="0"
                step="1"
                class="form-control"
                wire:change="updateSetting('img-shadow-y', $event.target.value)"
                value="{{ $element->getConfig('img-shadow-y', 1) }}"
        />
        <span class="input-group-text">{{ __('common.px') }}</span>
    </div>
    <div class="input-group mb-1">
        <span class="input-group-text">{{ __('people.id.image.shadow.blur') }}</span>
        <input
                type="number"
                min="0"
                step="1"
                class="form-control"
                wire:change="updateSetting('img-shadow-blur', $event.target.value)"
                value="{{ $element->getConfig('img-shadow-blur', 1) }}"
        />
        <span class="input-group-text">{{ __('common.px') }}</span>
    </div>
    <div class="input-group mb-3">
        <span class="input-group-text">{{ __('people.id.image.shadow.color') }}</span>
        <input
                type="color"
                class="form-control-color"
                wire:change="updateSetting('img-shadow-color', $event.target.value)"
                value="{{ $element->getConfig('img-shadow-color', "#000000") }}"
        />
    </div>
</li>
