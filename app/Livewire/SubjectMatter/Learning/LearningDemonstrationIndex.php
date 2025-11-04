<?php

namespace App\Livewire\SubjectMatter\Learning;

use App\Models\People\Person;
use Illuminate\Support\Collection;
use Livewire\Component;

class LearningDemonstrationIndex extends Component
{
	public array $breadcrumb;
	public Person $faculty;
	public Collection $courses;
	public int $selectedCourseId;
	
	public function mount($course = null)
	{
		$this->breadcrumb =
			[
				trans_choice('learning.demonstrations', 2) => '#',
			];
		$this->faculty = auth()->user();
		$classesTaught = $this->faculty->currentClassSessions;
		$this->courses = new Collection();
		foreach ($classesTaught as $session)
			$this->courses[$session->course->id] = $session->course;
		if($course && is_numeric($course))
			$this->selectedCourseId = $course;
		else
			$this->selectedCourseId = $this->courses->first()->id;
	}
	
    public function render()
    {
        return view('livewire.subject-matter.learning.learning-demonstration-index')
	        ->extends('layouts.app', ['breadcrumb' => $this->breadcrumb])
	        ->section('content');
    }
}
