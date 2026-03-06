<?php

use App\Mail\NewSubstituteVerification;
use App\Mail\NewSubstituteWelcomeEmail;
use App\Models\BB\Year;
use App\Models\Substitutes\CampusRequest;
use App\Models\Substitutes\Substitute;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;

new class extends Component
{
	public Substitute $substitute;
	public bool $active = false;
	public string $statusMessage = '';
	public $signedRequests;
	public string $yearFilter = '';
	public $availableYears;

	public function mount(Substitute $substitute)
	{
		$this->substitute = $substitute->load('campuses:id,name');
		$this->active = (bool)$this->substitute->active;
		$requestDates = CampusRequest::query()
			->where('substitute_id', $this->substitute->id)
			->with('subRequest:id,requested_for')
			->get()
			->map(fn($request) => $request->subRequest?->requested_for)
			->filter();

		$this->availableYears = Year::query()
			->orderByDesc('start')
			->get(['id', 'label', 'start', 'end'])
			->filter(function (Year $year) use ($requestDates)
			{
				if ($year->start->isFuture())
					return false;

				return $requestDates->contains(function ($date) use ($year)
				{
					return $date && $date->between($year->start, $year->end);
				});
			})
			->values();

		$currentYear = Year::currentYear();
		if ($currentYear && $this->availableYears->contains(fn($year) => (string)$year->id === (string)$currentYear->id))
			$this->yearFilter = (string)$currentYear->id;
		elseif ($this->availableYears->isNotEmpty())
			$this->yearFilter = (string)$this->availableYears->first()->id;

		$this->loadSignedRequests();
	}

	public function loadSignedRequests(): void
	{
		$query = CampusRequest::query()
			->where('substitute_id', $this->substitute->id)
			->join('sub_requests', 'sub_campus_requests.request_id', '=', 'sub_requests.id')
			->select('sub_campus_requests.*')
			->with([
				'subRequest:id,requester_id,requester_name,requested_for',
				'subRequest.requester:id,first,last,nick',
				'campus:id,name',
				'classRequests:id,campus_request_id,session_id,start_on,end_on',
				'classRequests.session:id,name,identifier',
			])
			->orderByDesc('sub_requests.requested_for');

		$selectedYear = Year::query()->find($this->yearFilter);
		if ($selectedYear)
			$query->whereBetween('sub_requests.requested_for', [$selectedYear->start, $selectedYear->end]);

		$this->signedRequests = $query->get();
	}

	public function updatedYearFilter(): void
	{
		$this->loadSignedRequests();
	}

	public function updatedActive(bool $value): void
	{
		$this->substitute->active = $value;
		$this->substitute->save();
		$this->statusMessage = $value ? 'Substitute enabled.' : 'Substitute disabled.';
	}

	public function resendWelcome(): void
	{
		Mail::to($this->substitute)->send(new NewSubstituteVerification($this->substitute));
		$this->statusMessage = 'Welcome email resent.';
	}
};
?>

<div class="py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <h1 class="h3 mb-0">{{ $substitute->name }}</h1>
        <a href="{{ route('substitutes.pool.index') }}" class="btn btn-outline-secondary">Back to Pool</a>
    </div>

    @if ($statusMessage !== '')
        <div x-data x-init="setTimeout(() => $wire.set('statusMessage', '') , 3000)">
            <div class="alert alert-success mb-3" role="alert">{{ $statusMessage }}</div>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <h2 class="h5 mb-0">Substitute Details</h2>
                        <div class="form-check form-switch mb-0">
                            <input
                                    class="form-check-input"
                                    type="checkbox"
                                    role="switch"
                                    id="enabled-switch"
                                    wire:model.live="active"
                            >
                            <label class="form-check-label" for="enabled-switch">
                                Enabled
                            </label>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img
                                src="{{ $substitute->portrait }}"
                                alt="{{ $substitute->name }}"
                                class="rounded-circle border"
                                style="width: 72px; height: 72px; object-fit: cover;"
                        >
                        <div>
                            <div class="text-muted small">Portrait</div>
                            <div class="fw-semibold">Profile Image</div>
                        </div>
                    </div>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Name</dt>
                        <dd class="col-sm-8">{{ $substitute->name }}</dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $substitute->email }}</dd>

                        <dt class="col-sm-4">Phone</dt>
                        <dd class="col-sm-8">{{ $substitute->prettyPhone() ?? '—' }}</dd>

                        <dt class="col-sm-4">Campuses</dt>
                        <dd class="col-sm-8">
                            @if ($substitute->campuses->isNotEmpty())
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach ($substitute->campuses as $campus)
                                        <span
                                                class="badge rounded-pill bg-light text-dark border">{{ $campus->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">No campuses assigned</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Communication</dt>
                        <dd class="col-sm-8">
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <span class="{{ $substitute->email_confirmed ? 'text-success' : 'text-danger' }}">
                                    <i class="bi bi-envelope-fill"></i>
                                    <span class="ms-1">Email</span>
                                </span>
                                <span class="{{ $substitute->sms_confirmed ? 'text-success' : 'text-danger' }}">
                                    <i class="bi bi-chat-dots-fill"></i>
                                    <span class="ms-1">Text</span>
                                </span>
                            </div>
                        </dd>
                    </dl>

                    <div class="d-flex flex-wrap align-items-center gap-2 mt-4">
                        <a href="{{ route('substitutes.pool.edit', $substitute) }}"
                           class="btn btn-sm btn-outline-primary">Edit</a>
                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resendWelcome">Resend
                            Welcome
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <h2 class="h5 mb-0">Signed-Up Requests</h2>
                        <div class="d-flex align-items-center gap-2">
                            <label for="year-filter" class="text-muted small mb-0">Year</label>
                            <select id="year-filter" class="form-select form-select-sm" style="width: auto;"
                                    wire:model.live="yearFilter">
                                @foreach ($availableYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse ($signedRequests as $campusRequest)
                            <div class="list-group-item px-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="fw-semibold">
                                        {{ $campusRequest->subRequest?->requested_for?->format('m/d/Y') ?? '—' }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $campusRequest->subRequest?->requester?->name ?? $campusRequest->subRequest?->requester_name ?? '—' }}
                                    </div>
                                    <div>
                                        <span class="badge rounded-pill bg-light text-dark border">
                                            {{ $campusRequest->campus?->name ?? 'Campus N/A' }}
                                        </span>
                                    </div>
                                    <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#classes-{{ $campusRequest->id }}"
                                            aria-expanded="false"
                                            aria-controls="classes-{{ $campusRequest->id }}"
                                            title="Show classes"
                                    >
                                        <i class="bi bi-caret-down-fill"></i>
                                    </button>
                                </div>

                                <div class="collapse mt-2" id="classes-{{ $campusRequest->id }}">
                                    <div class="border rounded-3 p-2 bg-light-subtle">
                                        <div class="small fw-semibold mb-1">Classes</div>
                                        @if ($campusRequest->classRequests->isNotEmpty())
                                            <ul class="mb-0 small">
                                                @foreach ($campusRequest->classRequests as $classRequest)
                                                    <li>
                                                        {{ $classRequest->session?->full_name ?? $classRequest->session?->name ?? ('Session #' . $classRequest->session_id) }}
                                                        @if ($classRequest->start_on && $classRequest->end_on)
                                                            ({{ $classRequest->start_on->format('g:i A') }}
                                                            - {{ $classRequest->end_on->format('g:i A') }})
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="small text-muted mb-0">No class details available for this
                                                request.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted">No signed-up requests yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>