<?php

use Livewire\Component;

new class extends Component
{
	public ClassRequest $classRequest;
	public string $subType = "subs";
	public Collection $results;
	public Collection $departments;
	public string $searchTerm = '';

	public function mount(ClassRequest $classRequest)
	{
		$this->classRequest = $classRequest;
		$this->listSubs();
		$this->departments = $this->classRequest->session->course->departments;
	}

	public function listSubs()
	{
		$this->results = Substitute::active()->whereHas('campuses', fn(Builder $query) => $query->where('campuses.id', $this->classRequest->campusRequest->campus_id))
			->get();
	}

	public function searchSubs()
	{
		$this->results = Substitute::active()->whereHas('campuses', fn(Builder $query) => $query->where('campuses.id', $this->classRequest->campusRequest->campus_id))
			->where('name', 'LIKE', '%' . $this->searchTerm . '%')
			->get();
	}

	public function listTeachers()
	{
		$teachers = new Collection;
		foreach ($this->departments as $department)
		{
			$teachers = $teachers->keyBy('id')->union($department->teachers()->keyBy('id'))->values();
		}
		$this->results = $teachers;
	}

	public function searchTeachers()
	{
		$this->results = Person::role(SchoolRoles::FACULTY->value)->search($this->searchTerm)->get();
	}

	public function search()
	{
		if(strlen($this->searchTerm) > 3)
		{
			if($this->subType == "subs")
				$this->searchSubs();
			else
				$this->searchTeachers();
		}
		else
		{
			if($this->subType == "subs")
				$this->listSubs();
			else
				$this->listTeachers();
		}
	}

	public function assignPerson(int $id)
	{
		$sub = $this->results->firstWhere('id', '=', $id);
		if($sub)
		{
			$this->classRequest->substitutable()->associate($sub);
			$this->classRequest->save();
			$this->dispatch('class-request-sub-assigner-assigned', classRequestId: $this->classRequest->id);
		}
	}
};
?>

<div>
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="h5 mb-1">Assign Coverage</h2>
                    <p class="text-muted mb-0">Assigning coverage for <span
                                class="fw-semibold">{{ $classRequest->session->name }}</span></p>
                </div>
            </div>

            <div class="row g-2 align-items-end mb-3">
                <div class="col-12 col-md-4">
                    <label for="assignee-type" class="form-label mb-1 text-muted small">Show</label>
                    <select id="assignee-type" wire:model="subType" wire:change="search" class="form-select form-select-sm">
                        <option value="subs">Substitutes</option>
                        <option value="teachers">Teachers ({{ $departments->pluck('name')->join(', ') }})</option>
                    </select>
                </div>

                <div class="col-12 col-md-8">
                    <label for="assignee-search" class="form-label mb-1 text-muted small">Search</label>
                    <input
                            id="assignee-search"
                            wire:model="searchTerm"
                            wire:keydown.debounce.500ms="search"
                            type="text"
                            class="form-control form-control-sm"
                            placeholder="Search by name"
                    >
                </div>
            </div>

            <div class="border rounded-3 bg-light-subtle p-2 mb-3">
                <div class="small text-muted fw-semibold mb-1">Legend</div>
                <ul class="small mb-0 ps-3">
                    <li><span class="fw-semibold"># Subbed</span>: The total number of times this person has subbed this class</li>
                    <li><span class="fw-semibold"># Total</span>: The total number of times this person has subbed this year</li>
                </ul>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Person</th>
                        @if($subType == "subs")
                            <th>Campuses</th>
                        @else
                            <th>Subjects</th>
                        @endif
                        <th class="text-center">#Subbed</th>
                        <th class="text-center">#Total</th>
                        <th class="text-end">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($results as $person)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img
                                            src="@if($subType == "subs") {{ $person->portrait }} @else {{ $person->thumb_url }} @endif"
                                            alt="Portrait of {{ $person->name }}"
                                            class="rounded-circle border"
                                            width="36"
                                            height="36"
                                    >
                                    <span class="fw-semibold">{{ $person->name }}</span>
                                </div>
                            </td>
                            <td>
                                @if($subType == "subs")
                                    {{ $person->campuses->implode('abbr', ', ') }}
                                @else
                                    {{ $person->departmentsTaught()->pluck('name')->join(', ') }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if($person->subbedClassSession($classRequest->session)->count() > 0)
                                    {{ $person->subbedClassSession($classRequest->session)->count() }}
                                @else
                                    No
                                @endif
                            </td>
                            <td class="text-center">
                                @if($person->totalSubbedInYear() > 0)
                                    {{ $person->totalSubbedInYear() }}
                                @else
                                    None
                                @endif
                            </td>
                            <td class="text-end">
                                <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        wire:click="assignPerson({{ $person->id }})"
                                >Assign</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">No people found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>