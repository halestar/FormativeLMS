<?php

use App\Models\People\Person;
use Illuminate\Support\Collection;
use Livewire\Component;

new class extends Component
{
	public Person $teacher;
	public Collection $campuses;
	public Collection $subjects;
	public bool $editing = false;
	public string $classes = "w-100";
	public string $style = "";
	public array $selectedSubjects = [];

	public function mount(Person $teacher)
	{
		if(!$teacher->isTeacher())
			throw new InvalidArgumentException("Passed person is not a teacher.");
		$this->teacher = $teacher;
		$this->campuses = $teacher->campuses;
		$this->subjects = $teacher->subjectsTaught;
		$this->selectedSubjects = $this->subjects->pluck('id')->toArray();
	}

	public function toggleEdit()
	{
		if($this->editing)
		{
			$this->teacher->subjectsTaught()->sync($this->selectedSubjects);
			$this->subjects = $this->teacher->subjectsTaught;
            $this->editing = false;
        }
		else
			$this->editing = true;
    }
};
?>

<div class="{{ $classes }}" style="{{ $style }}">
    @if(!$editing)
        <h6 class="d-flex justify-content-between align-items-baseline">
            <div>
                <strong class="me-2">{{ trans_choice('subjects.subject.taught',2) }}
                    :</strong> {{ $subjects->isEmpty()? __('subjects.subject.taught.no'):
                        $subjects->map(fn($subject) => $subject->name . " (" . $subject->campus->abbr . ")")->join(', ') }}
            </div>
            @can('people.edit')
                <button
                        type="button"
                        wire:click="toggleEdit"
                        class="btn btn-primary btn-sm rounded rounded-pill text-nowrap text-sm"
                >{{ __('subjects.subject.taught.edit') }}</button>
            @endcan
        </h6>
    @else
        <div class="card">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span>{{ __('subjects.subject.taught.assign') }}</span>
                <button
                        type="button"
                        class="btn btn-danger btn-sm rounded rounded-pill"
                        wire:click="toggleEdit"
                        aria-label="{{ trans('common.close') }}"
                >{{ __('subjects.subject.taught.editing') }}</button>
            </h5>
            <div class="card-body">
                @foreach($campuses as $campus)
                    <h6>{{ $campus->name }}</h6>
                    <div class="d-flex flex-wrap">
                        @foreach($campus->subjects as $subject)
                            <div class="form-check ms-3 mb-2" wire:key="{{ $subject->id }}">
                                <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="subject_{{ $subject->id }}"
                                        name="subjects[]"
                                        value="{{ $subject->id }}"
                                        wire:model="selectedSubjects"
                                />
                                <label class="form-check-label" for="subject_{{ $subject->id }}">{{ $subject->name }}</label>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>