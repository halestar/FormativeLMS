<?php


use App\Jobs\CreateSubstituteRequestNotifications;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use App\Models\Substitutes\SubstituteCampusRequest;
use App\Models\Substitutes\SubstituteClassRequest;
use App\Models\Substitutes\SubstituteRequest;
use App\Traits\FullPageComponent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
	use FullPageComponent;

	public Person $person;
	public string $date;
	public Collection $classes;
	public array $selectedClasses = [];
	public bool $requested = false;
	public bool $hasRequest = false;

	public function mount()
	{
		$this->person = auth()->user();
		$this->date = date('Y-m-d');
		$this->loadDate();
		$this->breadcrumb =
			[
				__('features.substitutes.request') => '#',
			];
	}

	public function loadDate()
	{
		$this->classes = $this->person->currentClassSessions
			->filter(fn ($class) => $class->meets($this->date))
			->sortBy(fn ($class) => $class->startTime($this->date));
		$this->selectedClasses = $this->classes->pluck('id')->toArray();
		//is there a subrequest for this?
		$this->hasRequest = SubstituteRequest::where('requester_id', $this->person->id)
			->whereDate('requested_for', date('Y-m-d', strtotime($this->date)))
			->exists();
	}

	public function updatedDate()
	{
		$this->loadDate();
	}

	public function toggleAll()
	{
		if (count($this->selectedClasses) == 0)
			$this->selectedClasses = $this->classes->pluck('id')->toArray();
		else
			$this->selectedClasses = [];
	}

	protected function messages()
	{
		return
			[
				'date' => __('features.substitutes.request.create.validation.date'),
				'selectedClasses' => __('features.substitutes.request.create.validation.selected_classes')
			];
	}

	protected function rules()
	{
		return
			[
				'date' => [
					'required',
					'date',
					Rule::date()->afterOrEqual(today())
				],
				'selectedClasses' => 'required|array|min:1',
			];
	}

	public function requestSub()
	{
		$this->validate();
		$date = Carbon::parse($this->date);
		$newRequest = new SubstituteRequest();
		$newRequest->requester_id = $this->person->id;
		$newRequest->requester_name = $this->person->name;
		$newRequest->requested_for = $date->format('Y-m-d');
		$newRequest->save();
		$campuses = [];
		foreach ($this->selectedClasses as $session_id)
		{
			$session = ClassSession::find($session_id);
			if (!$session)
				continue;
			$campus = $session->course->campus;
			if (!isset($campuses[$campus->id]))
			{
				$campusReq = new SubstituteCampusRequest();
				$campusReq->request_id = $newRequest->id;
				$campusReq->campus_id = $campus->id;
				$campusReq->save();
				$campuses[$campus->id] = $campusReq;
			}
			$classReq = new SubstituteClassRequest();
			$classReq->campus_request_id = $campuses[$campus->id]->id;
			$classReq->session_id = $session->id;
			$classReq->start_on = $session->startTime($this->date);
			$classReq->end_on = $session->endTime($this->date);
			$classReq->save();
		}
		CreateSubstituteRequestNotifications::dispatch($newRequest);
		$this->requested = true;
	}
};
?>

<div class="container py-4">
    @if($requested)
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <div class="text-center mb-4">
                    <h1 class="h3 mb-1">{{ __('features.substitutes.request.create.title') }}</h1>
                    <p class="text-muted mb-0">{{ __('features.substitutes.request.create.description') }}</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success fs-1"></i>
                        </div>

                        <div class="alert alert-success mb-0 text-start" role="alert">
                            <div class="fw-semibold mb-1">{{ __('features.substitutes.request.create.success') }}</div>
                            <a href="{{ route('features.substitutes.create') }}" class="alert-link">
                                {{ __('features.substitutes.request.create.request_another') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <form wire:submit="requestSub">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                <div>
                    <h1 class="h3 mb-1">{{ __('features.substitutes.request.create.title') }}</h1>
                    <p class="text-muted mb-0">{{ __('features.substitutes.request.create.description') }}</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-xl-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="mb-3">
                                <h2 class="h5 mb-1">{{ __('features.substitutes.request.create.date.heading') }}</h2>
                                <p class="text-muted small mb-0">{{ __('features.substitutes.request.create.date.description') }}</p>
                            </div>

                            <label for="request-date" class="form-label">{{ __('common.date') }}</label>
                            <input
                                    id="request-date"
                                    type="date"
                                    class="form-control @error('date') is-invalid @enderror"
                                    wire:model="date"
                                    wire:change="loadDate"
                                    min="{{ now()->toDateString() }}"
                            >
                            @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-8">
                    @if($classes->isNotEmpty() && !$hasRequest)
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                        <h2 class="h5 mb-1">{{ __('features.substitutes.request.create.classes.heading') }}</h2>
                                        <p class="text-muted small mb-0">{{ __('features.substitutes.request.create.classes.description') }}</p>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="toggleAll">
                                        {{ __('features.substitutes.request.create.classes.toggle_all') }}
                                    </button>
                                </div>

                                @error('selectedClasses')
                                <div class="alert alert-danger" role="alert">{{ $message }}</div>
                                @enderror

                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th>{{ __('features.substitutes.request.create.classes.cover') }}</th>
                                            <th>{{ trans_choice('subjects.class', 1) }}</th>
                                            <th>{{ __('features.substitutes.request.create.classes.start_time') }}</th>
                                            <th>{{ __('features.substitutes.request.create.classes.end_time') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($classes as $session)
                                            <tr wire:key="session-{{ $session->id }}">
                                                <td>
                                                    <div class="form-check mb-0">
                                                        <input
                                                                type="checkbox"
                                                                name="selectedClasses[]"
                                                                id="selectedClasses-{{ $session->id }}"
                                                                wire:model.live="selectedClasses"
                                                                value="{{ $session->id }}"
                                                                class="form-check-input"
                                                        >
                                                    </div>
                                                </td>
                                                <td class="fw-semibold">{{ $session->name }}</td>
                                                <td>{{ date('h:i A', strtotime($session->startTime($date))) }}</td>
                                                <td>{{ date('h:i A', strtotime($session->endTime($date))) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('features.substitutes.request.create.actions.submit') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @elseif($hasRequest)
                        <div class="alert alert-warning mb-0" role="alert">
                            {{ __('features.substitutes.request.create.state.existing_request') }}
                        </div>
                    @else
                        <div class="alert alert-warning mb-0" role="alert">
                            {{ __('features.substitutes.request.create.state.no_classes') }}
                        </div>
                    @endif
                </div>
            </div>
        </form>
    @endif
</div>