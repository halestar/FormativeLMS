<?php

use App\Models\People\Person;
use App\Models\SubjectMatter\Subject;
use App\Models\Substitutes\Substitute;
use App\Models\Substitutes\SubstituteClassRequest;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;

new class extends Component
{
	public SubstituteClassRequest $classRequest;
	public string $subType = "subs";
	public Subject $subject;
	public string $searchTerm = '';

	public function mount(SubstituteClassRequest $classRequest)
	{
		$this->classRequest = $classRequest;
		$this->subject = $this->classRequest->session->course->subject;
	}

	#[Computed]
	public function results(): Collection
	{
		if (strlen($this->searchTerm) > 2)
		{
			if ($this->subType == "subs")
				return Person::search($this->searchTerm)->whereIn('roles', SchoolRoles::$SUBSTITUTE)->get();
			if ($this->subType == "subject")
				return Person::search($this->searchTerm)->whereIn('roles', SchoolRoles::$FACULTY)->get();
			return Person::search($this->searchTerm)->whereIn('roles', SchoolRoles::$FACULTY)->get();
		}

		if ($this->subType == "subs")
		{
			return Person::substitutes()->with(['substituteProfile', 'substituteProfile.campuses'])
			             ->whereHas('substituteProfile.campuses', fn (
				             Builder $query) => $query->where('campuses.id', $this->subject->campus_id))->get();
		}
		if ($this->subType == "subject")
		{
			return Person::teachers()->with('subjectsTaught')
			             ->whereHas('subjectsTaught', fn (
				             Builder $query) => $query->where('subjects.id', $this->subject->id))
			             ->get();
		}
		return Person::teachers()->get();

	}


	public function assignPerson(int $id)
	{
		$sub = $this->results->firstWhere('id', '=', $id);
		if ($sub)
		{
			$this->classRequest->substitute()->associate($sub);
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
                    <h2 class="h5 mb-1">{{ __('features.substitutes.requests.coverage.assign') }}</h2>
                </div>
            </div>

            <div class="row g-2 align-items-end mb-3">
                <div class="col-12 col-md-4">
                    <label for="assignee-type" class="form-label mb-1 text-muted small">{{ __('common.show') }}</label>
                    <select id="assignee-type" wire:model.live="subType"
                            class="form-select form-select-sm">
                        <option value="subs">{{ trans_choice('features.substitutes', 1) }}</option>
                        <option value="subject">{{ __('common.faculty') }} ({{ $subject->name }})</option>
                        <option value="teachers">{{ __('common.faculty.all') }}</option>
                    </select>
                </div>

                <div class="col-12 col-md-8">
                    <label for="assignee-search"
                           class="form-label mb-1 text-muted small">{{ __('common.search') }}</label>
                    <input
                            id="assignee-search"
                            wire:model.live.debounce="searchTerm"
                            type="text"
                            class="form-control form-control-sm"
                            placeholder="{{ __('people.search.person') }}"
                    >
                </div>
            </div>

            <div class="border rounded-3 bg-light-subtle p-2 mb-3">
                <div class="small text-muted fw-semibold mb-1">{{ __('common.legend') }}</div>
                <ul class="small mb-0 ps-3">
                    <li>
                        <span class="fw-semibold">{{ __('features.substitutes.subbed.number') }}</span>:
                        {{ __('features.substitutes.subbed.number.description') }}
                    </li>
                    <li>
                        <span class="fw-semibold">{{ __('features.substitutes.subbed.total') }}</span>:
                        {{ __('features.substitutes.subbed.total.description') }}
                    </li>
                </ul>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>{{ __('people.name') }}</th>
                        @if($subType == "subs")
                            <th>{{ trans_choice('locations.campus', 2) }}</th>
                        @else
                            <th>{{ trans_choice('subjects.subject', 2) }}</th>
                        @endif
                        <th class="text-center">{{ __('features.substitutes.subbed.number') }}</th>
                        <th class="text-center">{{ __('features.substitutes.subbed.total') }}</th>
                        <th class="text-end">{{ __('common.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($this->results as $person)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img
                                            src="{{ $person->portrait_url->thumbUrl() }}"
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
                                    {{ $person->substituteProfile->campuses->implode('abbr', ', ') }}
                                @else
                                    {{ $person->subjectsTaught->pluck('name')->join(', ') }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if($subType == "subs")
                                    {{ $person->substituteProfile->totalSubbedInYear(schoolClass: $classRequest->session->schoolClass) }}
                                @else
                                    {{ $person->totalSubbedInYear(schoolClass: $classRequest->session->schoolClass) }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if($subType == "subs")
                                    {{ $person->substituteProfile->totalSubbedInYear() }}
                                @else
                                    {{ $person->totalSubbedInYear() }}
                                @endif
                            </td>
                            <td class="text-end">
                                <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        wire:click="assignPerson({{ $person->id }})"
                                >{{ __('common.assign') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">{{ __('common.results.no') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>