<div class="input-group mb-3">
    <span class="input-group-text">Import:</span>
    <select class="form-select" wire:model="importName">
        @foreach($imports as $name => $card)
            <option value="{{ $name }}">{{ $name }}</option>
        @endforeach
    </select>
    <button type="button" class="btn btn-primary" wire:click="import()">{{ __('common.import') }}</button>
</div>
<ul class="list-group">
    @foreach(config('lms.school_id_elements') as $element)
        <li
                class="list-group-item show-as-grab list-group-item-action d-flex align-items-center"
                draggable="true"
                x-on:dragstart="
            draggingType = 'element';
            draggingContent = '{!! str_replace('\\', '\\\\', $element) !!}';
        "
        >
            <span class="fs-4">{{ $element::getName() }}</span>
        </li>
    @endforeach
</ul>
