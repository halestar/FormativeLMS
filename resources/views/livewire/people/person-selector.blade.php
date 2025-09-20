<div class="{{ $containerClasses }}" x-data="
    {
        isTyped: false,
        idxCounter: 0,
        resultCount: $wire.entangle('resultCount'),
    }">
    <div x-on:click.outside="isTyped = false">
        <div class="position-relative">
            <input
                    type="text"
                    id="person-selector-search"
                    class="form-control position-relative"
                    placeholder="{{ $placeholder }}"
                    autocomplete="off"
                    wire:model.live.debounce.400ms="search"
                    aria-label="{{ $placeholder }}"
                    @keydown.arrow-down.prevent="idxCounter = ((idxCounter + 1) % resultCount)"
                    @keydown.arrow-up.prevent="idxCounter = (idxCounter === 0)? (resultCount - 1): (idxCounter - 1)"
                    x-on:input.debounce.300ms="isTyped = ($event.target.value.length > 2)"
                    x-on:focus="isTyped = ($event.target.value.length > 2)"
                    x-on:keydown.enter.prevent="$wire.selectPerson($('#result--'+idxCounter).attr('person_id')); isTyped = false"
                    @if($selectedPerson) disabled @endif
            />
            <a href="#" wire:click="clearSearch()"
               class="position-absolute top-50 end-0 translate-middle-y me-2 ps-2 fs-4 bg-secondary-subtle text-secondary border-start">
                <i class="fas fa-times-circle"></i>
            </a>
        </div>
        <div class="position-absolute w-100 z-3" x-show="isTyped" x-cloak>
            <ul class="list-group">
                @forelse($people as $person)
                    <li
                            wire:key="{{ $person->id }}"
                            person_id="{{ $person->id }}"
                            id="result--{{ $loop->index }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center cursor-pointer"
                            x-bind:class="idxCounter === {{ $loop->index }} ? 'active' : ''"
                            @if($loop->first)
                                x-init="idxCounter = 0"
                            @endif
                            wire:click.prevent="selectPerson({{ $person->id }})"
                            x-on:click="isTyped = false"
                    >
                        <div>
                            <img
                                    src="{{ $person->thumbnail_url }}"
                                    alt="{{ $person->name }}"
                                    width="32"
                                    height="32"
                                    class="img-fluid img-thumbnail rounded-circle me-2"
                            />
                            {{ $person->name }}
                        </div>
                        <div>
                            @if($person->isStudent())
                                <span class="badge bg-info">{{ __('common.student') }}</span>
                            @endif
                            @if($person->isEmployee())
                                <span class="badge bg-info">{{ trans_choice('people.employee', 1) }}</span>
                            @endif
                            @if($person->isParent())
                                <span class="badge bg-info">{{ __('common.parent') }}</span>
                            @endif
                        </div>
                    </li>
                @empty
                    <div class="list-group-item text-center fs-5 fw-bold">{{ __('common.results.no.found') }}</div>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@script
<script>
    $wire.on('person-selected', (event) => {
        {!! $selectedCB !!}(event.person);
    });

    $wire.on('person-cleared', (event) => {
        {!! $clearedCB !!}();
    });
</script>
@endscript
