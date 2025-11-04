<?php

namespace App\Livewire\SubjectMatter\Learning;

use App\Models\People\Person;
use App\Models\SubjectMatter\Learning\LearningDemonstrationTemplate;
use App\Models\SubjectMatter\Learning\LearningDemonstrationType;
use App\Models\Utilities\TemporaryFiler;
use Illuminate\Support\Collection;
use Livewire\Component;

class LearningDemonstrationCreator extends Component
{
	public array $breadcrumb;
	public Person $faculty;
	public Collection $courses;
	public Collection $demonstrationTypes;
	public int $selectedCourseId;
	public string $name = '';
	public string $abbr = '';
	public int $type;
	public string $strategy = '';
	public string $demonstration = '';
	public array $skills = [];
	
	protected function rules()
	{
		return
			[
				'name' => 'required|min:3|max:255',
				'abbr' => 'required|min:1|max:10',
				'type' => 'required|exists:learning_demonstration_types,id',
				'selectedCourseId' => 'required|exists:courses,id',
				'strategy' => 'required|in:assessment,demonstration',
				'demonstration' => 'required_if:strategy,demonstration|min:10',
				'skills' => 'required_if:strategy,assessment|array'
			];
	}
	public function mount($course = null)
	{
		$this->breadcrumb =
			[
				trans_choice('learning.demonstrations', 2) => route('learning.ld.index', $course),
				__('learning.demonstrations.new') => '#',
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
		$this->demonstrationTypes = LearningDemonstrationType::all();
		$this->type = $this->demonstrationTypes->first()->id;
	}
	
	public function setCourse()
	{
		$this->dispatch('skill-selector.set-course', course: $this->selectedCourseId);
	}
	
	public function create()
	{
		$this->validate();
		//now we create it!
		$ld = new LearningDemonstrationTemplate();
		$ld->person_id = $this->faculty->id;
		$ld->course_id = $this->selectedCourseId;
		$ld->type_id = $this->type;
		$ld->name = $this->name;
		$ld->abbr = $this->abbr;
		//demonstration?
		if($this->strategy == 'demonstration')
			$ld->demonstration = $this->demonstration;
		$ld->save();
		if($this->strategy == 'assessment')
			$ld->skills()->attach($this->skills);
		return $this->redirect(route('learning.ld.post', $ld), navigate: true);
	}
	
	public function render()
	{
		return view('livewire.subject-matter.learning.learning-demonstration-creator')
			->extends('layouts.app', ['breadcrumb' => $this->breadcrumb])
			->section('content');
	}
}
