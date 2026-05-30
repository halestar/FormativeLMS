<?php

use App\Classes\Settings\SchoolSettings;
use App\Models\People\Person;
use App\Models\Schedules\Period;
use App\Models\Substitutes\SubstituteCampusRequest;
use App\Models\Substitutes\SubstituteClassRequest;
use App\Models\Substitutes\SubstituteRequest;
use App\Traits\FullPageComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
	use FullPageComponent;

	public Person $person;
	public array $dates;
	public Collection $campuses;
	public int $campusSelected;

	#[Computed]
	public function days(): Collection
	{
		$settings = app()->make(SchoolSettings::class);
		$days = array_keys($settings->days);
		$startDay = min($days) % 7;
		$endDay = max($days) % 7;
		$startDate = Carbon::now()->startOfWeek($startDay);
		$endDate = $startDate->copy()->endOfWeek($endDay);
		$results = new Collection;
		for ($i = $startDate->copy(); $i->isBefore($endDate); $i->addDay())
		{
			$dayRecord =
				[
					'day' => $i->copy(),
					'periods' => Period::active()
					                   ->where('campus_id', $this->campusSelected)
					                   ->where('day', $i->dayOfWeekIso)
					                   ->get(),
					'requests' => [],
				];
			//get all the request for the day.
			$subRequests = SubstituteCampusRequest::where('campus_id', $this->campusSelected)
			                                      ->with(['subRequest', 'classRequests'])
			                                      ->whereHas('subRequest', fn (
				                                      Builder $query) => $query->where('requested_for', $i->format('Y-m-d')))
			                                      ->get();
			foreach ($subRequests as $subRequest)
			{
				$requestRecord =
					[
						'request' => $subRequest->subRequest,
						'periods' => [],
					];
				foreach ($subRequest->classRequests as $classRequest)
				{
					$meetsOn = $classRequest->session->periodsOnDate($i);
					if ($classRequest->hasSub())
					{
						$className = 'warning';
						$name = $classRequest->substitute->name;
					}
					elseif ($classRequest->campusRequest->hasSub())
					{
						$className = 'success';
						$name = $classRequest->campusRequest->substitute->name;
					}
					else
					{
						$className = "danger";
						$name = $classRequest->session->name;
					}
					foreach ($meetsOn as $meets)
						$requestRecord['periods'][$meets->id] = ['class' => $className, 'name' => $name];
				}
				$dayRecord['requests'][] = $requestRecord;
			}
			$results->push($dayRecord);
		}
		return $results;
	}

	public function mount()
	{
		$this->person = auth()->user();
		$this->campuses = $this->person->campuses;
		$this->campusSelected = $this->campuses->first()->id;
		$this->breadcrumb =
			[
				__('features.features') => '#',
				trans_choice('features.substitutes.requests', 2) => route('features.substitutes.index'),
				__('features.substitutes.matrix') => '#',
			];
	}

	public function setCampus()
	{
		$campusSelected = $this->campuses->first(fn ($item) => $item->id == $this->campusSelected);
		if ($campusSelected)
		{

			$this->person->prefs['working_campus_id'] = $this->campusSelected;
			$this->person->save();
			$this->createBlocks();
		}
	}
};
?>

<div class="container">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h3 mb-1">{{ __('features.substitutes.matrix') }}</h1>
        </div>
        <a href="{{ route('features.substitutes.index') }}"
           class="btn btn-outline-secondary">{{ __('features.substitutes.requests.back') }}</a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4 col-lg-3">
                    <label for="campus_id"
                           class="form-label mb-1 text-muted small">{{ trans_choice('locations.campus', 1) }}</label>
                    <select wire:model.live="campusSelected" id="campus_id" class="form-select form-select-sm">
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge text-bg-danger">{{ __('features.substitutes.assigned.no') }}</span>
                        <span class="badge text-bg-success">{{ __('features.substitutes.signed') }}</span>
                        <span class="badge text-bg-warning">{{ __('features.substitutes.assigned') }}</span>
                        <span class="badge bg-secondary-subtle text-secondary border">{{ trans_choice('subjects.class.no', 1) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @foreach($this->days as $date)
                <div class="{{ $loop->last ? '' : 'mb-4' }}">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                        <h2 class="h5 mb-0">{{ $date['day']->format('l') }}</h2>
                        <span class="text-muted small">{{ $date['day']->format('m/d/Y') }}</span>
                    </div>

                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                        <tr wire:key="day-{{ $loop->index }}">
                            <th class="text-muted small text-uppercase"
                                style="min-width: 180px;">{{ __('features.substitutes.requester') }}</th>
                            @forelse($date['periods'] as $period)
                                <th class="text-muted small text-uppercase text-center"
                                    style="min-width: 140px;">{{ $period->name }}</th>
                            @empty
                                <th class="text-muted small text-uppercase">{{ __('locations.period.no') }}</th>
                            @endforelse
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($date['requests'] as $request)
                            <tr wire:key="request-{{ $date['day']->format('Ymd') }}-{{ $request['request']->id }}">
                                <td class="fw-semibold">
                                    <a href="{{ route('features.substitutes.show', $request['request']->id) }}"
                                       class="text-decoration-none">
                                        {{ $request['request']->requester_name }}
                                    </a>
                                </td>
                                @foreach($date['periods'] as $period)
                                    @if(isset($request['periods'][$period->id]))
                                        <td class="table-{{ $request['periods'][$period->id]['class'] }} text-center">
                                            <a href="{{ route('features.substitutes.show', $request['request']->id) }}"
                                               class="text-decoration-none fw-semibold">
                                                {{ $request['periods'][$period->id]['name'] }}
                                            </a>
                                        </td>
                                    @else
                                        <td class="table-secondary"></td>
                                    @endif
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ max(count($date['periods']) + 1, 2) }}"
                                    class="text-center text-muted py-4">
                                    {{ __('features.substitutes.request.no') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</div>
