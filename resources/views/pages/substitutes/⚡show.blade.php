<?php

use App\Models\Locations\Year;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use App\Notifications\Substitutes\NewSubstituteVerification;
use App\Models\Substitutes\Substitute;
use App\Models\Substitutes\SubstituteCampusRequest;
use App\Traits\FullPageComponent;
use Livewire\Component;

new class extends Component
{
	use FullPageComponent;

	public Substitute $substitute;
	public bool $active = false;
	public string $statusMessage = '';
	public $signedRequests;
	public string $yearFilter = '';
	public $availableYears;

	public function mount(Person $person): void
	{
		$this->substitute = $person->substituteProfile;
		$this->active = $person->hasRole(SchoolRoles::$SUBSTITUTE);
		$requestDates = SubstituteCampusRequest::query()
			->where('substitute_id', $this->substitute->id)
			->with('subRequest:id,requested_for')
			->get()
			->map(fn ($request) => $request->subRequest?->requested_for)
			->filter();

		$this->breadcrumb = [
			__('features.features') => '#',
			trans_choice('features.substitutes.requests', 2) => route('features.substitutes.index'),
			__('features.substitutes.pool') => route('features.substitutes.pool.index'),
			trans_choice('features.substitutes', 1) => '#',
		];

		$this->availableYears = Year::query()
			->orderByDesc('year_start')
			->get([
				'id',
				'label',
				'year_start',
				'year_end'
			])
			->filter(function (Year $year) use ($requestDates)
			{
				if ($year->year_start->isFuture())
				{
					return false;
				}

				return $requestDates->contains(function ($date) use ($year)
				{
					return $date && $date->between($year->year_start, $year->year_end);
				});
			})
			->values();

		$currentYear = Year::currentYear();
		if (
			$currentYear
			&& $this->availableYears->contains(fn ($year) => (string)$year->id === (string)$currentYear->id)
		)
		{
			$this->yearFilter = (string)$currentYear->id;
		}
		elseif ($this->availableYears->isNotEmpty())
		{
			$this->yearFilter = (string)$this->availableYears->first()->id;
		}

		$this->loadSignedRequests();
	}

	public function toggleActivation()
	: void
	{
		$person = $this->substitute->person;
		if ($person->hasRole(SchoolRoles::$SUBSTITUTE))
		{
			$person->removeRole(SchoolRoles::$SUBSTITUTE);
			$person->assignRole(SchoolRoles::$OLD_SUBSTITUTE);
			$this->active = false;
		}
		else
		{
			if ($person->hasRole(SchoolRoles::$OLD_SUBSTITUTE))
				$person->removeRole(SchoolRoles::$OLD_SUBSTITUTE);
			$person->assignRole(SchoolRoles::$SUBSTITUTE);
			$this->active = true;
		}

		$this->statusMessage = $this->active
			? __('features.substitutes.pool.status.enabled')
			: __('features.substitutes.pool.status.disabled');
	}

	public function loadSignedRequests(): void
	{
		$query = SubstituteCampusRequest::query()
			->where('substitute_id', $this->substitute->id)
			->join('substitute_requests', 'substitute_campus_requests.request_id', '=', 'substitute_requests.id')
			->select('substitute_campus_requests.*')
			->with([
				'subRequest:id,requester_id,requester_name,requested_for',
				'subRequest.requester:id,first,last,nick',
				'campus:id,name',
				'classRequests:id,campus_request_id,session_id,start_on,end_on',
				'classRequests.session:id,name,identifier',
			])
			->orderByDesc('substitute_requests.requested_for');

		$selectedYear = Year::query()->find($this->yearFilter);
		if ($selectedYear)
		{
			$query->whereBetween('substitute_requests.requested_for', [
				$selectedYear->year_start,
				$selectedYear->year_end
			]);
		}

		$this->signedRequests = $query->get();
	}

	public function updatedYearFilter(): void
	{
		$this->loadSignedRequests();
	}

	public function resendWelcome(): void
	{
		$this->substitute->person->notify(new NewSubstituteVerification($this->substitute->person));
		$this->statusMessage = __('features.substitutes.pool.welcome.resent');
	}
};
?>

<div class="container py-4">
    @php($sessionStatus = session('status'))

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <h1 class="h3 mb-0">{{ $substitute->name }}</h1>
                <span class="badge {{ $active ? 'text-bg-success' : 'text-bg-warning' }}">
                    {{ $active ? __('common.active') : __('common.inactive') }}
                </span>
            </div>
            <p class="text-muted mb-0">{{ __('features.substitutes.pool.show.description') }}</p>
        </div>

        <a href="{{ route('features.substitutes.pool.index') }}" class="btn btn-outline-secondary">
            {{ __('features.substitutes.pool.back') }}
        </a>
    </div>

    @if ($sessionStatus)
        <div class="alert alert-success mb-3" role="alert">{{ $sessionStatus }}</div>
    @endif

    @if ($statusMessage !== '')
        <div x-data x-init="setTimeout(() => $wire.set('statusMessage', ''), 3000)">
            <div class="alert alert-success mb-3" role="alert">{{ $statusMessage }}</div>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="mb-4">
                        <h2 class="h5 mb-1">{{ __('features.substitutes.pool.details') }}</h2>
                        <p class="text-muted small mb-0">{{ __('features.substitutes.pool.details.description') }}</p>
                    </div>

                    <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                        <img
                                src="{{ $substitute->person->portrait_url }}"
                                alt="{{ $substitute->name }}"
                                class="rounded-circle border"
                                style="width: 72px; height: 72px; object-fit: cover;"
                        >
                        <div>
                            <div class="text-muted small">{{ __('people.portrait') }}</div>
                            <div class="fw-semibold">{{ __('people.profile.image') }}</div>
                        </div>
                    </div>

                    <dl class="row mb-4">
                        <dt class="col-sm-4">{{ __('people.name') }}</dt>
                        <dd class="col-sm-8">{{ $substitute->name }}</dd>

                        <dt class="col-sm-4">{{ __('people.profile.fields.email') }}</dt>
                        <dd class="col-sm-8">{{ $substitute->email }}</dd>

                        <dt class="col-sm-4">{{ __('phones.phone') }}</dt>
                        <dd class="col-sm-8">{{ $substitute->phone?->prettyPhone ?? __('common.na') }}</dd>

                        <dt class="col-sm-4">{{ trans_choice('locations.campus', 2) }}</dt>
                        <dd class="col-sm-8">
                            @if ($substitute->campuses->isNotEmpty())
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach ($substitute->campuses as $campus)
                                        <span class="badge rounded-pill bg-light text-dark border">{{ $campus->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">{{ __('locations.campus.no') }}</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">{{ __('settings.communications') }}</dt>
                        <dd class="col-sm-7">
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <span class="small {{ $substitute->email_confirmed ? 'text-success' : 'text-danger' }}">
                                    <i class="bi bi-envelope-fill"></i>
                                    <span class="ms-1">{{ __('people.profile.fields.email') }}</span>
                                </span>
                                <span class="small {{ $substitute->sms_confirmed ? 'text-success' : 'text-danger' }}">
                                    <i class="bi bi-chat-dots-fill"></i>
                                    <span class="ms-1">{{ __('settings.communications.sms') }}</span>
                                </span>
                            </div>
                        </dd>
                    </dl>

                    <div class="border rounded-3 p-3 mb-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <div class="fw-semibold">{{ __('features.substitutes.pool.status') }}</div>
                                <div class="text-muted small">{{ __('features.substitutes.pool.status.description') }}</div>
                            </div>

                            <div class="form-check form-switch m-0">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    role="switch"
                                    id="substitute-status-switch"
                                    wire:model.live="active"
                                    wire:change="toggleActivation"
                                >
                                <label class="form-check-label" for="substitute-status-switch">
                                    {{ $active ? __('common.active') : __('common.inactive') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="{{ route('features.substitutes.pool.edit', $substitute) }}"
                           class="btn btn-sm btn-outline-primary">
                            {{ __('common.edit') }}
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resendWelcome">
                            {{ __('features.substitutes.pool.welcome.resend') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <h2 class="h5 mb-0">{{ __('features.substitutes.pool.requests') }}</h2>
                                <span class="badge rounded-pill bg-light text-dark border">{{ $signedRequests->count() }}</span>
                            </div>
                            <p class="text-muted small mb-0">{{ __('features.substitutes.pool.requests.description') }}</p>
                        </div>

                        @if ($availableYears->isNotEmpty())
                            <div class="d-flex align-items-center gap-2">
                                <label for="year-filter"
                                       class="text-muted small mb-0">{{ __('features.substitutes.pool.year') }}</label>
                                <select id="year-filter" class="form-select form-select-sm" style="width: auto;"
                                        wire:model.live="yearFilter">
                                    @foreach ($availableYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="list-group list-group-flush">
                        @forelse ($signedRequests as $campusRequest)
                            <div class="list-group-item px-0 py-3">
                                <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3">
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                            <span class="fw-semibold">
                                                {{ $campusRequest->subRequest?->requested_for?->format('m/d/Y') ?? __('common.na') }}
                                            </span>
                                            <span class="badge rounded-pill bg-light text-dark border">
                                                {{ $campusRequest->campus?->name ?? __('common.na') }}
                                            </span>
                                        </div>
                                        <div class="text-muted small">
                                            {{ __('features.substitutes.requester') }}:
                                            <span class="text-body">
                                                {{ $campusRequest->subRequest?->requester?->name ?? $campusRequest->subRequest?->requester_name ?? __('common.na') }}
                                            </span>
                                        </div>
                                    </div>

                                    <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#classes-{{ $campusRequest->id }}"
                                            aria-expanded="false"
                                            aria-controls="classes-{{ $campusRequest->id }}"
                                            aria-label="{{ __('features.substitutes.pool.requests.classes.toggle') }}"
                                            title="{{ __('features.substitutes.pool.requests.classes.toggle') }}"
                                    >
                                        <i class="bi bi-caret-down-fill"></i>
                                    </button>
                                </div>

                                <div class="collapse mt-3" id="classes-{{ $campusRequest->id }}">
                                    <div class="border rounded-3 p-3 bg-light-subtle">
                                        <div class="small fw-semibold mb-2">{{ __('features.substitutes.pool.requests.classes') }}</div>

                                        @if ($campusRequest->classRequests->isNotEmpty())
                                            <ul class="mb-0 small ps-3">
                                                @foreach ($campusRequest->classRequests as $classRequest)
                                                    <li>
                                                        <span class="fw-medium">
                                                            {{ $classRequest->session?->full_name ?? $classRequest->session?->name ?? __('features.substitutes.pool.requests.classes.session_fallback', ['id' => $classRequest->session_id]) }}
                                                        </span>

                                                        @if ($classRequest->start_on && $classRequest->end_on)
                                                            <span class="text-muted">
                                                                ({{ $classRequest->start_on->format('g:i A') }} - {{ $classRequest->end_on->format('g:i A') }})
                                                            </span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="small text-muted mb-0">
                                                {{ __('features.substitutes.pool.requests.classes.empty') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">{{ __('features.substitutes.pool.requests.empty') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>