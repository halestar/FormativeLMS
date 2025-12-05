<?php

namespace App\Livewire\SubjectMatter\Learning;

use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Learning\LearningDemonstration;
use App\Models\SubjectMatter\Learning\LearningDemonstrationTemplate;
use Livewire\Component;

class LearningDemonstrationEditor extends Component
{
	public array $breadcrumb;
	public Person $faculty;
	public LearningDemonstration $ld;
	public ClassSession $classSession;
	public string $name = '';
	public string $abbr = '';
	public string $demonstration = '';
	public array $skills = [];
	public array $rubrics = [];
	public array $links = [];
	public array $questions = [];
	public array $assessments = [];
	public bool $allow_rating = false;
	public bool $online_submission = false;
	public bool $open_submission = false;
	public bool $submit_after_due = false;
	public bool $share_submissions = false;
	public array $postedClasses = [];
	public array $otherClasses = [];

	protected function rules()
	{
		return
			[
				'name' => 'required|min:3|max:255',
				'abbr' => 'required|min:1|max:10',
				'demonstration' => 'required|min:10',
				'skills' => 'required|array|min:1',
			];
	}

	public function mount(LearningDemonstration $ld, ClassSession $classSession)
	{
		$this->ld = $ld;
		$this->classSession = $classSession;
		$this->breadcrumb =
			[
				$this->classSession->name_with_schedule => route('subjects.school.classes.show', $this->classSession),
				__('learning.demonstrations.post') => '#',
			];
		$this->faculty = auth()->user();
		$this->name = $ld->name;
		$this->abbr = $ld->abbr;
		$this->demonstration = $ld->demonstration ?? "";
		$this->links = array_map(fn($link) => $link->toArray(), $ld->links);
		$this->questions = array_map(fn($question) => $question->toArray(), $ld->questions);
		foreach($this->ld->skills as $skill)
		{
			$this->skills[] = $skill->id;
			$this->assessments[$skill->id] =
				[
					'id' => $skill->assessment->id,
					'name' => $skill->prettyName(),
					'weight' => $skill->assessment->weight,
					'original_rubric' => $skill->rubric,
					'rubric' => $skill->assessment->rubric,
				];
		}
		//posting options
		$this->allow_rating = $ld->allow_rating;
		$this->online_submission = $ld->online_submission;
		$this->open_submission = $ld->open_submission;
		$this->submit_after_due = $ld->submit_after_due;
		$this->share_submissions = $ld->share_submissions;
		//posted classes
		$this->postedClasses = [];
		foreach($ld->classSessions as $session)
		{
			$opportunities = [];
			foreach($session->session->opportunities as $opportunity)
				$opportunities[$opportunity->student_id] = $opportunity;
			$students = [];
			foreach($session->students as $student)
			{
				$students[$student->id] =
				[
					'name' => $student->person->name,
					'posted' => isset($opportunities[$student->id]),
					'post' => isset($opportunities[$student->id]) ? $opportunities[$student->id]->posted_on->format('Y-m-d\TH:i') :
						$session->session->posted_on->format('Y-m-d\TH:i'),
					'due' => isset($opportunities[$student->id]) ? $opportunities[$student->id]->due_on->format('Y-m-d\TH:i') :
						$session->session->due_on->format('Y-m-d\TH:i'),
					'completed' => isset($opportunities[$student->id]) && $opportunities[$student->id]->completed,
					'submitted' => isset($opportunities[$student->id]) && $opportunities[$student->id]->submitted_on,
					'criteria_weight' => isset($opportunities[$student->id]) ? $opportunities[$student->id]->criteria_weight : $session->session->criteria_weight,
				];
			}

			$this->postedClasses[$session->id] =
				[
					'post' => $session->session->posted_on->format('Y-m-d\TH:i'),
					'due' => $session->session->due_on->format('Y-m-d\TH:i'),
					'class' => $session->name_with_schedule,
					'students' => $students,
					'criteria' => $session->classCriteria->pluck('name', 'id')->toArray(),
					'criteria_id' => $session->session->criteria_id,
					'criteria_weight' => $session->session->criteria_weight,
				];
		}
	}


    public function render()
    {
        return view('livewire.subject-matter.learning.learning-demonstration-editor')
	        ->extends('layouts.app', ['breadcrumb' => $this->breadcrumb])
	        ->section('content');
    }
}
