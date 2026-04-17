<?php

use App\Classes\Settings\Days;
use App\Models\Locations\Campus;
use App\Models\Locations\Term;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\SchoolClass;
use App\Traits\FullPageComponent;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
	use FullPageComponent;

	public SchoolClass $schoolClass;
	public Campus $campus;
	public Collection $sessions;
	public Collection $subjectTeachers;
	public ?ClassSession $classSession = null;

	//class session parameters
	public string $scheduleType = "block";
	public string $room_type = "single";
	public ?int $room_id = null;
	public ?int $block_id = null;
	public array $periods = [];
	public Collection $teachers;
	public array $multiple_rooms = [];

	public function mount(SchoolClass $schoolClass)
	{
		Gate::authorize('update', $schoolClass);
		$this->breadcrumb =
			[
				$schoolClass->course->campus->name => route('locations.campuses.show',
					['campus' => $schoolClass->course->campus->id]),
				$schoolClass->subject->name => route('subjects.subjects.index',
					['campus' => $schoolClass->course->campus->id]),
				$schoolClass->course->name => route('subjects.courses.index',
					['subject' => $schoolClass->course->subject_id]),
				trans_choice('subjects.class', 2) => route('subjects.classes.index',
					['course' => $schoolClass->course_id]),
				__('subjects.class.edit') => '#',
			];
		$this->schoolClass = $schoolClass;
		$this->sessions = $this->schoolClass->sessions;
		$this->campus = $schoolClass->course->campus;
		$this->loadSession($this->sessions->first());
	}

	public function loadSession(?ClassSession $classSession)
	{

		$this->classSession = $classSession;
		if ($this->classSession)
		{
			foreach (Days::getWeekdays() as $day)
				$this->periods[$day] = [];
			$this->room_id = $this->classSession->room_id;
			if ($classSession->block_id)
			{
				$this->scheduleType = "block";
				$this->block_id = $classSession->block_id;
			}
			else
			{
				$this->scheduleType = "periods";
				if ($this->room_id)
					$this->room_type = "single";
				else
				{
					$this->room_type = "multiple";
					$this->multiple_rooms = [];
					foreach ($this->classSession->periods as $period)
						$this->multiple_rooms[$period->id] = $period->sessionPeriod->room_id;
				}
				foreach (Days::getWeekdays() as $day)
					$this->periods[$day] = $this->classSession->periods()
						->where('day', $day)
						->pluck('id')
						->toArray();
			}
			$this->teachers = $classSession->teachers;
			$this->subjectTeachers = $this->schoolClass->subject->teachers->whereNotIn('id', $this->teachers->pluck('id'));
		}

	}

	public function createClassSession(Term $term)
	{
		$classSession = new ClassSession();
		$classSession->class_id = $this->schoolClass->id;
		$classSession->term_id = $term->id;
		$classSession->save();
		$this->sessions = $this->schoolClass->sessions;
		$this->loadSession($classSession);
	}

	public function save()
	{
		if ($this->classSession)
		{
			$this->saveData($this->classSession);
		}
		else
		{
			foreach ($this->sessions as $session)
				$this->saveData($session);
		}
		return redirect()->route('subjects.classes.index', $this->schoolClass->course->id);
	}

	private function saveData(ClassSession $classSession)
	{
		$classSession->room_id = $this->room_id;
		if ($this->scheduleType == "block")
		{
			$classSession->block()
				->associate($this->block_id);
			$classSession->periods()
				->detach();
		}
		else
		{
			$classSession->block()
				->dissociate();
			if ($this->room_type == "multiple")
			{
				$classSession->room_id = null;
				$periods = [];
				foreach ($this->multiple_rooms as $period_id => $room_id)
					$periods[$period_id] = ['room_id' => $room_id];
				$classSession->periods()
					->sync($periods);
			}
			else
			{
				$periods = [];
				foreach ($this->periods as $day)
					$periods = array_merge($periods, $day);
				foreach ($periods as $period_id)
					$periods[$period_id] = ['room_id' => null];
				$classSession->periods()
					->sync($periods);
			}
		}
		$classSession->teachers()
			->sync($this->teachers->pluck('id'));
		$classSession->save();
	}

	public function setTerm(?Term $term = null)
	{
		$this->loadSession($term ? $this->schoolClass->termSession($term) : null);
	}

	public function addTeacher(Person $teacher)
	{
		$this->teachers->push($teacher);
		$this->subjectTeachers = $this->schoolClass->subject->teachers->whereNotIn('id', $this->teachers->pluck('id'));
	}

	#[On('person-selected')]
	public function personSelected(string $instance, $person_id)
	{
		if ($instance == "teacher-search")
		{
			$person = Person::find($person_id);
			if($person)
			{
				$this->addTeacher($person);
				$this->dispatch('person-search-clear', instance: "teacher-search");
			}
		}
	}

	public function removeTeacher(Person $teacher)
	{
		$this->teachers->forget($this->teachers->search($teacher));
		$this->subjectTeachers = $this->schoolClass->subject->teachers->whereNotIn('id', $this->teachers->pluck('id'));
	}
};
?>
<div class="container py-3 py-lg-4">
    <div class="row g-4 align-items-start">
        <div class="col-12 col-lg-3 col-xl-2">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-header bg-body-tertiary border-0">
                    <div class="fw-semibold small text-uppercase text-body-secondary">
                        {{ trans_choice('locations.terms', $schoolClass->year->campusTerms($schoolClass->course->campus)->count()) }}
                    </div>
                </div>
                <div class="list-group list-group-flush rounded-0">
                    <button
                            type="button"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center @if(!$classSession) active @endif"
                            wire:click="setTerm()"
                            @if(!$classSession) aria-current="page" @endif
                    >
                        <span>{{ __('subjects.class.manager.terms.all') }}</span>
                        @if(!$classSession)
                            <span class="badge text-bg-light text-dark">{{ __('common.active') }}</span>
                        @endif
                    </button>
                    @foreach($schoolClass->year->campusTerms($schoolClass->course->campus)->get() as $term)
                        @if($schoolClass->sessions()->where('term_id', $term->id)->exists())
                            <button
                                    type="button"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center @if($classSession && $classSession->term_id == $term->id) active @endif"
                                    wire:click="setTerm({{ $term->id }})"
                                    @if($classSession && $classSession->term_id == $term->id) aria-current="page" @endif
                            >
                                <span>{{ $term->label }}</span>
                                @if($classSession && $classSession->term_id == $term->id)
                                    <i class="fa-solid fa-check"></i>
                                @endif
                            </button>
                        @else
                            <div class="list-group-item bg-body-tertiary">
                                <button
                                        type="button"
                                        class="btn btn-outline-primary btn-sm w-100"
                                        wire:click="createClassSession({{ $term->id }})"
                                >{{ __('subjects.class.manager.terms.add', ['term' => $term->label]) }}</button>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-9 col-xl-10">
            <div class="card shadow-sm border-0">
                @if(!$classSession)
                    <div class="alert alert-warning rounded-0 rounded-top border-0 border-bottom mb-0">
                        <div class="d-flex align-items-center gap-2 fw-semibold">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>{{ __('subjects.class.manager.terms.all.warning') }}</span>
                        </div>
                    </div>
                @endif

                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 border-bottom pb-3 mb-4">
                        <div>
                            <div class="text-uppercase small text-body-secondary fw-semibold mb-1">
                                {{ trans_choice('subjects.class', 1) }}
                            </div>
                            <h2 class="h3 mb-0">{{ $schoolClass->name }}</h2>
                        </div>
                        <span class="badge rounded-pill text-bg-primary px-3 py-2 fs-6 fw-medium align-self-start align-self-lg-center">
                            {{ $classSession?->term?->label ?? __('subjects.class.manager.terms.all') }}
                        </span>
                    </div>

                    <div class="row g-4">
                        <div class="col-12 col-xl-7">
                            <div class="card h-100 border bg-body-tertiary shadow-sm">
                                <div class="card-body p-3 p-lg-4">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                                        <h3 class="h5 mb-0">{{ __('subjects.class.schedule') }}</h3>
                                        <span class="badge rounded-pill text-bg-light border text-body-secondary">
                                            {{ __('subjects.class.manager.schedule.type') }}
                                        </span>
                                    </div>

                                    <div class="mb-4">
                                        <div class="btn-group" role="group" aria-label="{{ __('subjects.class.manager.schedule.type') }}">
                                            <input
                                                    class="btn-check"
                                                    type="radio"
                                                    id="schedule_block"
                                                    name="schedule_type"
                                                    value="block"
                                                    wire:model.live="scheduleType"
                                            />
                                            <label class="btn btn-outline-primary" for="schedule_block">{{ trans_choice('locations.block', 1) }}</label>

                                            <input
                                                    class="btn-check"
                                                    type="radio"
                                                    id="schedule_periods"
                                                    name="schedule_type"
                                                    value="periods"
                                                    wire:model.live="scheduleType"
                                            />
                                            <label class="btn btn-outline-primary" for="schedule_periods">{{ trans_choice('locations.period', 2) }}</label>
                                        </div>
                                    </div>

                                    @if($scheduleType == "block")
                                        <div class="row g-3">
                                            <div class="col-12 col-md-6">
                                                <label for="block_id" class="form-label fw-semibold">{{ trans_choice('locations.block', 1) }}</label>
                                                <select id="block_id" class="form-select shadow-sm" wire:model="block_id">
                                                    <option>{{ __('subjects.class.manager.block.select') }}</option>
                                                    @foreach($campus->blocks as $block)
                                                        <option value="{{ $block->id }}">{{ $block->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="room_id" class="form-label fw-semibold">{{ trans_choice('locations.rooms', 1) }}</label>
                                                <select id="room_id" class="form-select shadow-sm" wire:model="room_id">
                                                    <option>{{ __('subjects.class.manager.room.select') }}</option>
                                                    @foreach($campus->rooms as $room)
                                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-center align-items-start gap-1 mb-3">
                                                @foreach(\App\Classes\Settings\Days::weekdaysOptions() as $dayId => $dayName)
                                                    <div class="border rounded-3 bg-white p-3 h-100 shadow-sm flex-fill">
                                                        <label for="day-select-{{ $dayId }}" class="form-label fw-semibold d-block text-center mb-3">
                                                            {{ Days::dayAbbr($dayId) }}
                                                        </label>
                                                        <select
                                                                class="form-select @error('periods') is-invalid @enderror"
                                                                id="day-select-{{ $dayId }}"
                                                                name="periods[]"
                                                                multiple
                                                                wire:model.live="periods.{{ $dayId }}"
                                                                size="{{ $campus->periods($dayId)->active()->count() }}"
                                                        >
                                                            @foreach($campus->periods($dayId)->active()->get() as $period)
                                                                <option value="{{ $period->id }}">{{ $period->abbr }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="border rounded-3 bg-white p-3 shadow-sm mb-4">
                                            <div class="fw-semibold mb-3">{{ trans_choice('locations.rooms', 1) }}</div>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="form-check form-check-inline mb-0">
                                                    <input
                                                            class="form-check-input"
                                                            type="radio"
                                                            id="room_single"
                                                            name="room_type"
                                                            value="single"
                                                            wire:model.live="room_type"
                                                            @if($room_type == "single") checked @endif
                                                    />
                                                    <label class="form-check-label" for="room_single">{{ __('subjects.class.manager.room.single') }}</label>
                                                </div>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input
                                                            class="form-check-input"
                                                            type="radio"
                                                            id="room_multiple"
                                                            name="room_type"
                                                            value="multiple"
                                                            wire:model.live="room_type"
                                                            @if($room_type == "multiple") checked @endif
                                                    />
                                                    <label class="form-check-label" for="room_multiple">{{ __('subjects.class.manager.room.multiple') }}</label>
                                                </div>
                                            </div>
                                        </div>

                                        @if($room_type == "single")
                                            <div>
                                                <label for="room_id" class="form-label fw-semibold">{{ trans_choice('locations.rooms', 1) }}</label>
                                                <select id="room_id" class="form-select shadow-sm" wire:model="room_id">
                                                    <option>{{ __('subjects.class.manager.room.select') }}</option>
                                                    @foreach($campus->rooms as $room)
                                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @else
                                            <div class="row g-3">
                                                @foreach(\App\Classes\Settings\Days::getWeekdays() as $dayId)
                                                    @foreach($periods[$dayId] as $period)
                                                        <div class="col-12 col-lg-6" wire:key="{{ $period }}">
                                                            <div class="border rounded-3 bg-white p-3 h-100 shadow-sm">
                                                                <label for="room-period-{{ $period }}" class="form-label fw-semibold">{{ \App\Models\Schedules\Period::find($period)->name }}</label>
                                                                <select id="room-period-{{ $period }}" class="form-select" wire:model="multiple_rooms.{{ $period }}">
                                                                    <option>{{ __('locations.rooms.no') }}</option>
                                                                    @foreach($campus->rooms as $room)
                                                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-xl-5">
                            <div class="card h-100 border bg-body-tertiary shadow-sm">
                                <div class="card-body p-3 p-lg-4 d-flex flex-column gap-4">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                                            <h3 class="h5 mb-0">{{ trans_choice('subjects.class.teacher', $teachers->count()) }}</h3>
                                            <span class="badge rounded-pill text-bg-primary">{{ $teachers->count() }}</span>
                                        </div>

                                        @if($teachers->isNotEmpty())
                                            <div class="list-group list-group-flush rounded-3 overflow-hidden border shadow-sm">
                                                @foreach($teachers as $teacher)
                                                    <div class="list-group-item d-flex justify-content-between align-items-center gap-3 py-3">
                                                        <div class="d-flex align-items-center gap-3 min-w-0">
                                                            <img
                                                                    src="{{ $teacher->portrait_url->thumbUrl() }}"
                                                                    class="rounded-circle border object-fit-cover flex-shrink-0"
                                                                    alt="{{ $teacher->name }}"
                                                                    style="width: 52px; height: 52px;"
                                                            />
                                                            <div class="min-w-0">
                                                                <div class="fw-semibold fs-5 text-truncate">{{ $teacher->name }}</div>
                                                            </div>
                                                        </div>
                                                        <button
                                                                type="button"
                                                                class="btn btn-outline-danger btn-sm rounded-pill"
                                                                wire:click="removeTeacher({{ $teacher->school_id }})"
                                                        >
                                                            <i class="fa-solid fa-times me-1"></i>{{ __('common.delete') }}
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="border rounded-3 bg-white text-center text-body-secondary py-4 px-3 shadow-sm">
                                                {{ __('common.results.no.found') }}
                                            </div>
                                        @endif
                                    </div>

                                    @if($subjectTeachers->isNotEmpty())
                                        <div class="border rounded-3 bg-white p-3 shadow-sm">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h4 class="h6 mb-0">{{ trans_choice('subjects.subject.teachers', $this->subjectTeachers->count()) }}</h4>
                                                <span class="badge rounded-pill text-bg-light border text-body-secondary">{{ $this->subjectTeachers->count() }}</span>
                                            </div>
                                            <div class="list-group list-group-flush rounded-3 overflow-hidden border">
                                                @foreach($subjectTeachers as $teacher)
                                                    <button
                                                            type="button"
                                                            class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3"
                                                            wire:click="addTeacher({{ $teacher->school_id }})"
                                                    >
                                                        <img
                                                                src="{{ $teacher->portrait_url->thumbUrl() }}"
                                                                class="rounded-circle border object-fit-cover flex-shrink-0"
                                                                alt="{{ $teacher->name }}"
                                                                style="width: 44px; height: 44px;"
                                                        />
                                                        <span class="fw-medium fs-5">{{ $teacher->name }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div>
                                        <div class="fw-semibold mb-2">{{ __('people.search.teacher') }}</div>
                                        <livewire:people.person-search
                                                instance="teacher-search"
                                                :roles-filtered="\App\Models\Utilities\SchoolRoles::$FACULTY"
                                                :placeholder="__('people.search.teacher')"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-md-row gap-3 mt-4 pt-4 border-top">
                        <button
                                type="button"
                                class="btn btn-primary btn-lg px-4"
                                wire:click="save()"
                        >{{ trans_choice('subjects.class.update', 1) }}</button>
                        <a
                                class="btn btn-outline-secondary btn-lg px-4"
                                href="{{ route('subjects.classes.index', ['course' => $schoolClass->course_id]) }}"
                        >{{ __('common.cancel') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
