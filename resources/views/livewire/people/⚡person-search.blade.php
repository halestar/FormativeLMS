<?php

use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
	public ?string $search = null;
	public int $maxResults = 5;
	public int $resultCount = 0;
	public string $instance;
	public Collection $people;
	public Collection $filterRoles;
	public string $placeholder;
	public string $classes = "w-100";
	public string $style = "";

	public function mount(string $instance, Collection|array|string|null $rolesFilter = null)
	{
		$this->instance = $instance;
		$this->filterRoles = new Collection();
		if ($rolesFilter)
		{
			if (is_array($rolesFilter))
				$this->filterRoles = new Collection($rolesFilter);
			elseif ($rolesFilter instanceof Collection)
				$this->filterRoles = $rolesFilter;
			elseif (is_string($rolesFilter))
				$this->filterRoles = new Collection([$rolesFilter]);
		}
		$this->people = new Collection();
		$this->placeholder = $this->placeholder ?? __('people.search.person');
	}

	public function selectPerson(int $personId)
	{
		$selectedPerson = $this->people->where('id', $personId)->first();
		if ($selectedPerson)
		{
			$this->search = "";
			$this->dispatch('person-selected', instance: $this->instance, person_id: $selectedPerson->id);
		}
	}

	#[On('person-search-clear')]
	public function clearSearch(string $instance)
    {
		if($instance == $this->instance)
    		$this->search = "";
    }

	public function render()
	{
		if ($this->search && strlen($this->search) > 2)
		{
			$people = Person::search($this->search);
			if ($this->filterRoles->count() > 0)
				$people = $people->role($this->filterRoles);
			$this->people = $people->take($this->maxResults)->get();
			$this->resultCount = $this->people->count();
		}
		else
		{
			$this->resultCount = 0;
			$this->people = new Collection();
		}
		return $this->view();
	}
};
?>
<div
        class="{{ $classes }}"
        style="{{ $style }}"
        x-data="
        {
            isTyped: false,
            idxCounter: 0,
            resultCount: $wire.entangle('resultCount'),
        }"

>
    <div x-on:click.outside="isTyped = false" class="position-relative">
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
            />
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
                                    src="{{ $person->portrait_url->thumbUrl() }}"
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
                            @if($person->isSubstitute())
                                <span class="badge bg-info">{{ __('common.substitute') }}</span>
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
