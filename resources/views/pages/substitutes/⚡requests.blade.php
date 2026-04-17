<?php

use App\Models\Substitutes\SubstituteClassRequest;
use App\Models\Substitutes\SubstituteRequest;
use App\Models\Substitutes\SubstituteToken;
use App\Notifications\Substitutes\NewRequestSubNotification;
use App\Traits\FullPageComponent;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
	use WithPagination, FullPageComponent;

	protected string $paginationTheme = 'bootstrap';

	public SubstituteRequest $subRequest;
	public Collection $invites;
	public Collection $classRequests;
	public Collection $coverageCampusRequests;
	public ?string $invitesSent = null;
	public bool $resolvingInternally = false;
	public ?SubstituteClassRequest $selectedClassRequest = null;
	public bool $showOtherRequests = false;
	public int $perPage = 25;

	private function reloadInvites()
	{
		$this->breadcrumb =
        [
	        __('features.features') => '#',
	        trans_choice('features.substitutes.requests', 2) => route('features.substitutes.index'),
	        trans_choice('features.substitutes.requests', 1) => '#',
        ];
		$this->invites = $this->subRequest
			->subTokens()
			->with([
				'substitute',
				'substitute.campuses'
			])
			->get()
			->sort(fn ($a, $b) => strcmp($a->substitute->name, $b->substitute->name));
	}

	public function mount(SubstituteRequest $subRequest)
	{
		$this->authorize('substitutes.admin');
		$this->subRequest = $subRequest;
		$this->classRequests = $this->subRequest->classRequests()->orderBy('start_on')->get();
		$this->reloadInvites();
		$this->coverageCampusRequests = $this->subRequest->campusRequests;
		$this->resolvingInternally = $this->subRequest->isResolvingInternally();

		$user = auth()->user();
		$this->perPage = $user->getPreference('items_per_page', 25);
	}

	#[Computed]
	public function otherRequests()
	{
		$referenceDate = $this->subRequest->requested_for?->toDateString() ?? now()->toDateString();

		return SubstituteRequest::query()
			->with(['campusRequests.campus:id,name'])
			->withCount('classRequests')
			->where('requester_id', $this->subRequest->requester_id)
			->thisYear()
			->whereKeyNot($this->subRequest->id)
			->whereDate('requested_for', '<', $referenceDate)
			->orderByDesc('requested_for')
			->paginate($this->perPage, ['*'], 'other_requests_page');
	}

	public function updatePerPage(): void
	{
		$value = (int)$this->perPage;
		if (!in_array($value, [
			10,
			25,
			50,
			100
		], true))
			$value = 25;

		$this->perPage = $value;
		$this->resetPage('other_requests_page');

		$user = auth()->user();
		$user->setPreference('items_per_page', $value);
		$user->save();
	}

	public function resendInvite(string $token): void
	{
		//first, we load the token
		$subToken = SubstituteToken::find($token);
		if ($subToken && $subToken->request_id == $this->subRequest->id)
		{
			$subToken->regenerateToken();
			//re-send the invite
			$subToken->substitute->person->notify(new NewRequestSubNotification($this->subRequest, $subToken->plainTextToken));
			$this->invitesSent = $subToken->token;
		}
		$this->reloadInvites();
	}

	public function resendAllInvites(): void
	{
		foreach ($this->invites as $invite)
		{
			$invite->regenerateToken();
			$invite->substitute->person->notify(new NewRequestSubNotification($this->subRequest, $invite->plainTextToken));
		}
		$this->reloadInvites();
		$this->invitesSent = "all";
	}

	public function selectClassRequest(int $id)
	{
		$this->selectedClassRequest = $this->classRequests->firstWhere('id', '=', $id);
	}

	public function resolveInternally(): void
	{
		//compete the subrequest
		$this->subRequest->completed = true;
		$this->subRequest->save();
		//remove all the invitations
		$this->subRequest->subTokens()->delete();
		//set the flag
		$this->resolvingInternally = true;
	}

	#[On('class-request-sub-assigner-assigned')]
	public function subAssigned($classRequestId)
	{
		if ($classRequestId == $this->selectedClassRequest->id)
		{
			$this->classRequests = $this->subRequest->classRequests()->orderBy('start_on')->get();
			$this->selectedClassRequest = null;
		}
	}

	public function removeAssignment(int $id)
	{
		$classRequest = $this->classRequests->firstWhere('id', '=', $id);
		if ($classRequest)
		{
			$classRequest->substitutable()->disassociate();
			$classRequest->save();
		}
		$this->classRequests = $this->subRequest->classRequests()->orderBy('start_on')->get();
	}

};
?>
<div class="container">
    @if ($invitesSent != null)
        <div
                class="alert alert-success mb-3"
                role="alert"
                x-data
                x-init="setTimeout(() => $el.remove(), 3000)"
        >
            @if ($invitesSent == "all")
                All invites have been re-issued.
            @else
                Invite for {{ $invites->firstWhere('token', $invitesSent)->substitute->name }} has been re-issued.
            @endif
        </div>
    @endif


    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <h1 class="h3 mb-0">Substitute Request</h1>
        <a href="{{ route('features.substitutes.index') }}" class="btn btn-outline-secondary">Back to Requests</a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Request Details</h2>

                    <dl class="row mb-0">
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            <span
                                    class="badge {{ $resolvingInternally? 'text-bg-warning': ($subRequest->completed ? 'text-bg-success' : 'text-bg-danger') }}">
                                {{ $resolvingInternally? "Resolving Internally" : ($subRequest->completed ? 'Completed' : 'Open') }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Requester</dt>
                        <dd class="col-sm-8">
                            <a href="{{ route('people.show', $subRequest->requester_id) }}"
                               class="text-decoration-none">{{ $subRequest->requester_name }}</a>
                        </dd>

                        <dt class="col-sm-4">Date</dt>
                        <dd class="col-sm-8">{{ $subRequest->requested_for?->format('m/d/Y') ?? '—' }}</dd>

                        <dt class="col-sm-4">Time Period</dt>
                        <dd class="col-sm-8">
                            {{ $subRequest->startTime()->format('h:i A') }}
                            - {{ $subRequest->endTime()->format('h:i A') }}
                        </dd>
                    </dl>
                    <hr class="my-3">

                    <h3 class="h6 text-uppercase text-muted mb-2">Classes To Cover</h3>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Class</th>
                                <th>Room</th>
                                <th>Start</th>
                                <th>End</th>
                                @if($resolvingInternally)
                                    <th></th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($classRequests as $classRequest)
                                <tr wire:key="class-request-{{ $classRequest->id }}">
                                    <td class="fw-semibold">{{ $classRequest->session?->name ?? '—' }}</td>
                                    <td>{{ $classRequest->session?->room?->name ?? '—' }}</td>
                                    <td>{{ $classRequest->start_on?->format('h:i A') ?? '—' }}</td>
                                    <td>{{ $classRequest->end_on?->format('h:i A') ?? '—' }}</td>
                                    @if($resolvingInternally)
                                        <td>
                                            @if($classRequest->hasSub())
                                                {{ $classRequest->substitutable->name }}
                                                <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-danger ms-2"
                                                        wire:click="removeAssignment({{ $classRequest->id }})"
                                                ><i class="bi bi-trash"></i></button>
                                            @elseif($selectedClassRequest && $selectedClassRequest->id == $classRequest->id)
                                                <button
                                                        type="button"
                                                        class="btn btn-sm btn-primary disabled"
                                                        disabled
                                                >
                                                    Assigning
                                                </button>
                                            @else
                                                <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-primary"
                                                        wire:click="selectClassRequest({{ $classRequest->id }})"
                                                >
                                                    Assign
                                                </button>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No classes found for this
                                        request.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                        <h3 class="h6 text-uppercase text-muted mb-0">Other Requests</h3>
                        <div class="form-check form-switch mb-0">
                            <input
                                    class="form-check-input"
                                    type="checkbox"
                                    role="switch"
                                    id="show-other-requests"
                                    wire:model.live="showOtherRequests"
                            >
                            <label class="form-check-label small" for="show-other-requests">
                                Show
                            </label>
                        </div>
                    </div>

                    @if ($showOtherRequests)
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <label for="other-requests-per-page" class="form-label mb-0 text-muted small">Entries
                                    per page</label>
                                <select
                                        id="other-requests-per-page"
                                        class="form-select form-select-sm"
                                        style="width: auto;"
                                        wire:model="perPage"
                                        wire:change="updatePerPage"
                                >
                                    @foreach ([10, 25, 50, 100] as $size)
                                        <option value="{{ $size }}">{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <nav aria-label="Other requests pagination">
                                {{ $this->otherRequests->links('layouts.small_paginator') }}
                            </nav>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Classes</th>
                                    <th>Campuses</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($this->otherRequests as $otherRequest)
                                    @php
                                        $campusNames = $otherRequest->campusRequests
                                            ->map(fn($campusRequest) => $campusRequest->campus?->name)
                                            ->filter()
                                            ->unique()
                                            ->values();
                                    @endphp
                                    <tr>
                                        <td>{{ $otherRequest->requested_for?->format('m/d/Y') ?? '—' }}</td>
                                        <td>
                                            {{ $otherRequest->startTime()->format('h:i A') }}
                                            -
                                            {{ $otherRequest->endTime()->format('h:i A') }}
                                        </td>
                                        <td>{{ $otherRequest->class_requests_count }}</td>
                                        <td>{{ $campusNames->isNotEmpty() ? $campusNames->join(', ') : '—' }}</td>
                                        <td>
                                            <span class="badge {{ $otherRequest->completed ? 'text-bg-success' : 'text-bg-danger' }}">
                                                {{ $otherRequest->completed ? 'Completed' : 'Open' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('features.substitutes.show', $otherRequest) }}"
                                               class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">No other requests found for
                                            this year.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if(!$resolvingInternally && !$subRequest->completed)
                        <div class="d-flex justify-content-end mt-3">
                            <button
                                    type="button"
                                    class="btn btn-sm btn-outline-warning"
                                    wire:click="resolveInternally"
                            >
                                Resolve Internally
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    @if($resolvingInternally)
                        @if($selectedClassRequest)
                            <livewire:substitutes.class-request-sub-assigner
                                    :classRequest="$selectedClassRequest"
                                    wire:key="sub-assigner-{{ $selectedClassRequest->id }}"
                            />
                        @else
                            <div class="alert alert-info mb-3" role="alert">
                                Select a class request to assign a substitute.
                            </div>
                        @endif
                    @elseif($subRequest->completed)
                        <h2 class="h5 mb-3">Assigned Coverage</h2>
                        <div class="list-group list-group-flush">
                            @forelse($coverageCampusRequests as $campusRequest)
                                <div class="list-group-item px-0">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                        <div class="fw-semibold">
                                            @if($campusRequest->substitute)
                                                <a href="{{ route('features.substitutes.pool.show', $campusRequest->substitute) }}"
                                                   class="text-decoration-none">
                                                    {{ $campusRequest->substitute->name }}
                                                </a>
                                            @else
                                                Covered Internally
                                            @endif
                                        </div>
                                        <span class="badge rounded-pill bg-light text-dark border">
                                            {{ $campusRequest->campus?->name ?? 'Campus N/A' }}
                                        </span>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                            <tr>
                                                <th>Class</th>
                                                <th>Room</th>
                                                <th>Start</th>
                                                <th>End</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($campusRequest->classRequests as $classRequest)
                                                <tr>
                                                    <td class="fw-semibold">{{ $classRequest->session?->name ?? '—' }}</td>
                                                    <td>{{ $classRequest->session?->room?->name ?? '—' }}</td>
                                                    <td>{{ $classRequest->start_on?->format('h:i A') ?? '—' }}</td>
                                                    <td>{{ $classRequest->end_on?->format('h:i A') ?? '—' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-2">No class data
                                                        for this campus.
                                                    </td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted">No assigned coverage details found.</div>
                            @endforelse
                        </div>
                    @else
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    wire:click="resendAllInvites"
                            >
                                Resend All Invites
                            </button>
                            <h2 class="h5 mb-0">Invited Substitutes</h2>
                        </div>

                        <div class="list-group list-group-flush">
                            @forelse($invites as $invite)
                                <div class="list-group-item px-0">
                                    <div
                                            class="d-flex flex-column flex-lg-row gap-2 gap-lg-3 align-items-start align-items-lg-center">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-start gap-2">
                                                <img
                                                        src="{{ $invite->substitute->person->portrait_url->thumbUrl() }}"
                                                        alt="{{ $invite->substitute->name }}"
                                                        class="rounded-circle border flex-shrink-0"
                                                        style="width: 32px; height: 32px; object-fit: cover;"
                                                >
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold">
                                                        <a href="{{ route('features.substitutes.pool.show', $invite->substitute) }}"
                                                           class="text-decoration-none">
                                                            {{ $invite->substitute->name }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-wrap gap-1 mt-2">
                                                @forelse($invite->substitute?->campuses ?? collect() as $campus)
                                                    <span
                                                            class="badge rounded-pill bg-light text-dark border">{{ $campus->name }}</span>
                                                @empty
                                                    <span class="text-muted small">No campuses assigned</span>
                                                @endforelse
                                            </div>
                                            <div class="small text-muted mt-2">
                                                Sent:
                                                {{ $invite->created_at->format('m/d/Y h:i A') ?? 'Not tracked' }}
                                                <span class="mx-1">|</span>
                                                Expires:
                                                <span
                                                        class="{{ $invite->expires_at && $invite->expires_at->isPast() ? 'text-danger fw-semibold' : '' }}">
                                                    {{ $invite->expires_at?->format('m/d/Y h:i A') ?? '—' }}
                                                    @if($invite->expires_at && $invite->expires_at->isPast())
                                                        (Expired)
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ms-lg-auto text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-3 mb-2">
                                                <span class="small">
                                                    <i class="bi bi-envelope-fill {{ $invite->substitute?->email_confirmed ? 'text-success' : 'text-danger' }}"></i>
                                                    <span class="ms-1">Email</span>
                                                </span>
                                                <span class="small">
                                                    <i class="bi bi-chat-dots-fill {{ $invite->substitute?->sms_confirmed ? 'text-success' : 'text-danger' }}"></i>
                                                    <span class="ms-1">Text</span>
                                                </span>
                                            </div>
                                            <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    wire:click="resendInvite('{{ $invite->token }}')"
                                            >
                                                Resend Invite
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted">No invited substitutes found for this request.</div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>