<?php


use App\Models\People\Person;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
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
	}

	public function loadDate()
	{
		$date = date('Y-m-d', strtotime($this->date));
		$this->classes = $this->person->classesTaught()
			->select(['room_events.start AS start_time', 'room_events.end AS end_time', 'class_sessions.*'])
			->join('room_events', 'room_events.session_id', '=', 'class_sessions.id')
			->whereDate('room_events.start', $date)
			->orderBy('room_events.start')
			->get();
		$this->selectedClasses = $this->classes->pluck('id')->toArray();
		//is there a subrequest for this?
		$this->hasRequest = SubRequest::where('requester_id', $this->person->id)
			->whereDate('requested_for', $date)
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
				'date' => 'You must select a valid date in the future',
				'selectedClasses' => 'You must select at least one class to cover.'
			];
	}

	protected function rules()
	{
		return
			[
				'date' => ['required', 'date', Rule::date()->afterOrEqual(today())],
				'selectedClasses' => 'required|array|min:1',
			];
	}

	public function requestSub()
	{
		$this->validate();
		$date = Carbon::parse($this->date);
		$newRequest = new SubRequest();
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
				$campusReq = new CampusRequest();
				$campusReq->request_id = $newRequest->id;
				$campusReq->campus_id = $campus->id;
				$campusReq->save();
				$campuses[$campus->id] = $campusReq;
			}
			$classReq = new ClassRequest();
			$classReq->campus_request_id = $campuses[$campus->id]->id;
			$classReq->session_id = $session->id;
			$classReq->start_on = $session->startTime($this->date);
			$classReq->end_on = $session->endTime($this->date);
			$classReq->save();
		}
		CreateSubRequestNotifications::dispatch($newRequest);
		$this->requested = true;
	}
};
?>

<div>
    @if($requested)
        <div class="py-4">
            <div class="alert alert-success" role="alert">
                Your substitute request has been submitted.
                <a href="{{ route('substitutes.create') }}">Request another one</a>
            </div>
        </div>
    @else
        <form wire:submit="requestSub">
            <div class="py-4">
                <div class="mb-4">
                    <h1 class="h3 mb-1">New Substitute Request</h1>
                    <p class="text-muted mb-0">
                        This form is used by faculty to request a substitute for the whole day or for certain
                        classes in a day.
                    </p>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="col-12 col-md-4">
                            <label for="request-date" class="form-label">Date</label>
                            <input
                                    id="request-date"
                                    type="date"
                                    class="form-control @error('date') is-invalid @enderror"
                                    wire:model="date"
                                    wire:change="loadDate"
                                    min="{{ now()->toDateString() }}"
                            >
                            @error('date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                @if($classes->isNotEmpty() && !$hasRequest)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h2 class="h5 mb-0">Select Classes You Need Coverage</h2>
                            <button type="button" class="btn btn-primary btn-sm" wire:click="toggleAll">Toggle All
                            </button>
                        </div>
                        <div class="card-body">
                            @error('selectedClasses')
                            <div class="alert alert-danger">{{ $message }}</div> @enderror
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th>Cover?</th>
                                        <th>Class</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($classes as $session)
                                        <tr wire:key="session-{{ $session->id }}">
                                            <td>
                                                <input
                                                        type="checkbox"
                                                        name="selectedClasses[]"
                                                        id="selectedClasses-{{ $session->id }}"
                                                        wire:model.live="selectedClasses"
                                                        value="{{ $session->id }}"
                                                        class="form-check-input border border-dark"
                                                >
                                            </td>
                                            <td>{{ $session->name }}</td>
                                            <td>{{ date('h:i A', strtotime($session->start_time)) }}</td>
                                            <td>{{ date('h:i A', strtotime($session->end_time)) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Request Substitute</button>
                    </div>
                @elseif($hasRequest)
                    <div class="alert alert-warning mb-3">You already have a request for this date.</div>
                @else
                    <div class="alert alert-warning mb-3">There are no classes in the selected date.</div>
                @endif
            </div>
        </form>
    @endif
</div>