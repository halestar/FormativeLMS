<li class="list-group-item">
    <h5 class="mb-3">{{ __('people.id.text.custom') }}</h5>
    <textarea
            class="form-control"
            wire:change="updateSetting('custom-text', $event.target.value)"
    >{{ $element->getConfig('custom-text', 0) }}</textarea>
</li>
