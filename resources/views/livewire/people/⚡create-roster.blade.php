<?php

use App\Models\People\Person;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
	public string $instance;
	public Collection $filterRoles;
	public string $placeholder;
	public string $classes = "mh-100 overflow-y-auto";
	public string $style = "";
	#[Modelable]
	public array $person_ids = [];

	public function mount(string $instance, Collection|array|string|null $rolesFilter = null)
	{
		$this->placeholder = __('people.search.person');
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
	}

	#[Computed]
	public function people(): Collection
	{
		if (count($this->person_ids) == 0)
			return new Collection;
		return Person::whereIn('id', $this->person_ids)->get();
	}

	#[On('person-selected')]
	public function addPerson(string $instance, int $person_id)
	{
		if ($instance == "person-add-roster")
		{
			$this->person_ids[] = $person_id;
			$this->person_ids = array_values(array_unique($this->person_ids));
		}
	}

	public function removePerson($person_id)
	{
		$this->person_ids = array_values(array_filter($this->person_ids, fn ($pid) => $pid != $person_id));
	}
};
?>

<div class="{{ $classes }}"
     style="{{ $style }}"
>
    <div class="mb-3">
        <livewire:people.person-search instance="person-add-roster" :roles-filter="$filterRoles"
                                       :placeholder="$placeholder"/>
    </div>

    <ul class="list-group shadow-sm border-0">
        @forelse ($this->people as $person)
            <li class="list-group-item d-flex justify-content-between align-items-center border-primary border-opacity-25 mb-2 rounded"
                wire:key="roster-{{$instance}}-{{ $person->id }}">
                <input type="hidden" name="{{ $instance }}[]" value="{{ $person->id }}"/>

                <div class="d-flex align-items-center">
                    <img
                            src="{{ $person->portrait_url->thumbUrl() }}"
                            alt="{{ $person->name }}"
                            class="img-fluid rounded-circle border border-2 border-primary me-3"
                            style="width: 48px; height: 48px; object-fit: cover;"
                    />
                    <div>
                        <h6 class="mb-1 fw-bold text-primary">{{ $person->name }}</h6>
                        <div class="d-flex gap-1">
                            @if($person->isStudent())
                                <span class="badge bg-info">{{ __('common.student') }}</span>
                            @endif
                            @if($person->isEmployee())
                                <span class="badge bg-info">{{ trans_choice('people.employee', 1) }}</span>
                            @endif
                            @if($person->isTeacher())
                                <span class="badge bg-info">{{ trans_choice('people.faculty', 1) }}</span>
                            @endif
                            @if($person->isParent())
                                <span class="badge bg-info">{{ __('common.parent') }}</span>
                            @endif
                            @if($person->isSubstitute())
                                <span class="badge bg-info">{{ __('common.substitute') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle shadow-sm"
                        style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;"
                        wire:click="removePerson({{ $person->id }})" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </li>
        @empty
            <li class="list-group-item bg-light text-center py-5 text-muted border border-secondary border-opacity-25 rounded"
                style="border-style: dashed !important;">
                <i class="fas fa-users fs-2 mb-3 text-secondary opacity-50"></i>
                <h6 class="fw-semibold mb-0">{{ __('common.content.empty') }}</h6>
            </li>
        @endforelse
    </ul>
</div>