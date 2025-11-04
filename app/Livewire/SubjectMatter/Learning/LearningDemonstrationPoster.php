<?php

namespace App\Livewire\SubjectMatter\Learning;

use App\Classes\Learning\DemonstrationQuestion;
use App\Classes\Learning\UrlResource;
use App\Models\People\Person;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\Learning\LearningDemonstrationTemplate;
use Livewire\Attributes\On;
use Livewire\Component;

class LearningDemonstrationPoster extends Component
{
	public array $breadcrumb;
	public Person $faculty;
	public LearningDemonstrationTemplate $ld;
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
	
	protected function rules()
	{
		return
			[
				'name' => 'required|min:3|max:255',
				'abbr' => 'required|min:1|max:10',
				'demonstration' => 'required|min:10',
			];
	}
	
	public function mount(LearningDemonstrationTemplate $ld)
	{
		$this->ld = $ld;
		$this->breadcrumb =
			[
				trans_choice('learning.demonstrations', 2) => route('learning.ld.index', $ld->course_id),
				__('learning.demonstrations.post') => '#',
			];
		$this->faculty = auth()->user();
		$this->name = $ld->name;
		$this->abbr = $ld->abbr;
		$this->demonstration = $ld->demonstration;
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
	}
	
	public function post()
	{
		//$this->validate();
	}
	
	#[On('skill-selector.skills-added')]
	public function addSkill(Skill $skill)
	{
		//first, is this an existing association?
		if($this->ld->skills()->where('skills.id', $skill->id)->exists())
		{
			$relatedSkill = $this->ld->skills()->where('skills.id', $skill->id)->first();
			$assessment =
				[
					'id' => $relatedSkill->assessment->id,
					'skill_id' => $relatedSkill->skill_id,
					'name' => $skill->prettyName(),
					'weight' => $relatedSkill->assessment->weight,
					'original_rubric' => $skill->rubric,
					'rubric' => $relatedSkill->assessment->rubric,
				];
		}
		else
		{
			$assessment =
				[
					'id' => "new_" . uniqid(),
					'skill_id' => $skill->id,
					'name' => $skill->prettyName(),
					'weight' => 1,
					'original_rubric' => $skill->rubric,
					'rubric' => $skill->rubric,
				];
		}
		$this->assessments[$skill->id] = $assessment;
	}
	
	#[On('skill-selector.skills-removed')]
	public function removeSkill(Skill $skill)
	{
		unset($this->assessments[$skill->id]);
	}
	
	public function updateTemplate()
	{
		//$this->validate();
		$this->ld->name = $this->name;
		$this->ld->abbr = $this->abbr;
		$this->ld->demonstration = $this->demonstration;
		$this->ld->links = array_map(fn($link) => UrlResource::hydrate($link), $this->links);
		$this->ld->questions = array_map(fn($question) => DemonstrationQuestion::hydrate($question), $this->questions);
		$this->ld->allow_rating = $this->allow_rating;
		$this->ld->online_submission = $this->online_submission;
		$this->ld->open_submission = $this->open_submission;
		$this->ld->submit_after_due = $this->submit_after_due;
		$this->ld->share_submissions = $this->share_submissions;
		$this->ld->save();
		//skills are next
		$skills = [];
		foreach($this->assessments as $skillId => $assessment)
			$skills[$skillId] = ['rubric' => $assessment['rubric'], 'weight' => $assessment['weight']];
		$this->ld->skills()->sync($skills);
	}
	
    public function render()
    {
	    return view('livewire.subject-matter.learning.learning-demonstration-poster')
		    ->extends('layouts.app', ['breadcrumb' => $this->breadcrumb])
		    ->section('content');
    }
}
