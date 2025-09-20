<div class="accordion" id="id-elements-container">
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button @if($selectedElement) collapsed @endif" type="button"
                    data-bs-toggle="collapse" data-bs-target="#id-settings" aria-expanded="true"
                    aria-controls="id-settings">
                {{ __('people.id.properties') }}
            </button>
        </h2>
        <div id="id-settings" class="accordion-collapse collapse @if(!$selectedElement) show @endif"
             data-bs-parent="#id-elements-container">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <h5>{{ __('people.id.dimensions') }}</h5>
                    <div class="input-group">
                        <span class="input-group-text">{{ __('people.id.rows') }}</span>
                        <input
                                type="number"
                                min="1"
                                step="1"
                                class="form-control"
                                wire:change="updateRowsNumber($event.target.value)"
                                value="{{ $idCard->rows }}"
                        />
                        <span class="input-group-text">{{ __('people.id.columns') }}</span>
                        <input
                                type="number"
                                min="1"
                                step="1"
                                class="form-control"
                                wire:change="updateColumnsNumber($event.target.value)"
                                value="{{ $idCard->columns }}"
                        />
                    </div>
                </li>
                <li class="list-group-item">
                    <h5>{{ __('people.id.background') }}</h5>
                    <div class="input-group">
                        <span class="input-group-text">{{ __('people.id.background-color') }}</span>
                        <input
                                type="color"
                                class="form-control-color"
                                wire:change="updateBackgroundColor($event.target.value)"
                                value="{{ $idCard->backgroundColor }}"
                        />
                    </div>
                    <div class="mb-2">
                        <label for="background-color-opacity"
                               class="form-label">{{ __('people.id.background-color-opacity') }}</label>
                        <input
                                type="range"
                                class="form-range"
                                id="background-color-opacity"
                                min="0"
                                max="1"
                                step="0.01"
                                value="{{ $idCard->backgroundImageOpacity }}"
                                wire:change="updateBackgroundImageOpacity($event.target.value)"
                        />
                    </div>
                    <div class="input-group">
                        <span class="input-group-text">{{ __('people.id.background-image') }}</span>
                        <input
                                type="url"
                                class="form-control"
                                wire:change="updateBackgroundImage($event.target.value)"
                                value="{{ $idCard->backgroundImage }}"
                        />
                    </div>
                    <div class="input-group mb-1">
                        <span class="input-group-text">{{ __('people.id.background-blend') }}</span>
                        <select
                                class="form-select"
                                wire:change="updateBackgroundBlendMode($event.target.value)"
                        >
                            <option value="normal">{{ __('people.id.background-blend.normal') }}</option>
                            <option value="darken">{{ __('people.id.background-blend.darken') }}</option>
                            <option value="lighten">{{ __('people.id.background-blend.lighten') }}</option>
                            <option value="multiply">{{ __('people.id.background-blend.multiply') }}</option>
                            <option value="screen">{{ __('people.id.background-blend.screen') }}</option>
                            <option value="overlay">{{ __('people.id.background-blend.overlay') }}</option>
                            <option value="soft-light">{{ __('people.id.background-blend.soft-light') }}</option>
                            <option value="hard-light">{{ __('people.id.background-blend.hard-light') }}</option>
                            <option value="difference">{{ __('people.id.background-blend.difference') }}</option>
                            <option value="exclusion">{{ __('people.id.background-blend.exclusion') }}</option>
                            <option value="hue">{{ __('people.id.background-blend.hue') }}</option>
                            <option value="saturation">{{ __('people.id.background-blend.saturation') }}</option>
                        </select>
                    </div>
                </li>
                <li class="list-group-item">
                    <h5>Spacing</h5>
                    <div class="input-group mb-1">
                        <span class="input-group-text">{{ __('people.id.content-padding') }}</span>
                        <input
                                type="number"
                                min="0"
                                step="1"
                                class="form-control"
                                wire:change="updateContentPadding($event.target.value)"
                                value="{{ $idCard->contentPadding }}"
                        />
                        <span class="input-group-text">{{ __('common.px') }}</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    @if($selectedElement)
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#selected-settings" aria-expanded="true" aria-controls="selected-settings">
                    {{ $selectedElement::getName() }}
                </button>
            </h2>
            <div id="selected-settings" class="accordion-collapse collapse show"
                 data-bs-parent="#id-elements-container">
                {!! $selectedElement->controlComponent() !!}
            </div>
        </div>
    @endif
</div>
