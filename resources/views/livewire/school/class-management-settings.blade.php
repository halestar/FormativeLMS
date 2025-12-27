<div classes="{!! $classes !!}" @if($style) style="{!! $style !!}" @endif>
    <h3 class="border-bottom">{{ __('school.classes.management') }}</h3>
    @foreach($schoolClass->sessions as $session)
        <h4>{{ $session->term->label }}</h4>
        @if($session->class_management_id)
            <div class="alert alert-info">
                {{ __('school.classes.management.assigned', ['service' => $session->classManager->service->name]) }}
            </div>
        @else
            <div class="alert alert-warning">
                {{ __('school.classes.management.no') }}
            </div>
        @endif
        <div class="input-group my-3">
            <span class="input-group-text">{{ __('school.classes.management.service') }}</span>
            <select name="class_management_id" id="class_management_id" class="form-select" wire:model="sessionConnections.{{ $session->id }}">
                <option value="">{{ __('school.classes.management.select') }}</option>"
                @foreach($connections as $connection)
                    <option value="{{ $connection->id }}">{{ $connection->service->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary" wire:click="apply({{ $session->id }})">{{ __('common.apply') }}</button>
            <button class="btn btn-warning" wire:click="applyToAll({{ $session->id }})">{{ __('common.apply.all') }}</button>
        </div>
    @endforeach
</div>
