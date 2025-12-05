<?php

namespace App\Livewire\SubjectMatter\Learning;

use App\Classes\Learning\DemonstrationQuestion;
use App\Classes\Learning\UrlResource;
use App\Events\Learning\DemonstrationPostedEvent;
use App\Models\Locations\Year;
use App\Models\People\Person;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Learning\ClassCriteria;
use App\Models\SubjectMatter\Learning\LearningDemonstration;
use App\Models\SubjectMatter\Learning\LearningDemonstrationClassSession;
use App\Models\SubjectMatter\Learning\LearningDemonstrationOpportunity;
use App\Models\SubjectMatter\Learning\LearningDemonstrationOpportunityAssessment;
use App\Models\SubjectMatter\Learning\LearningDemonstrationTemplate;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
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
    public bool $canUseAI = false;
	public array $courseClasses = [];
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
        // Do we have system AI permissions?
        $this->canUseAI = $this->faculty->canUseAi();
		//classes to post to
		$currentClasses = $this->faculty->currentClassSessions;
		foreach($currentClasses as $classSession)
		{
			if($classSession->schoolClass->course_id == $ld->course_id)
			{
				$this->courseClasses[$classSession->id] =
					[
						'selected' => true,
						'post' => date('Y-m-d\TH:i'),
						'due' => date('Y-m-d\TH:i'),
						'class' => $classSession->full_name,
						'students' => [],
						'post_to_class' => true,
						'criteria' => $classSession->classCriteria->pluck('name', 'id')->toArray(),
						'criteria_id' => $classSession->classCriteria->first()?->id?? null,
						'criteria_weight' => 1,
					];
			}
			else
			{
				if(!isset($this->otherClasses[$classSession->schoolClass->course_id]))
				{
					$this->otherClasses[$classSession->schoolClass->course_id] =
						['course' => $classSession->course->name, 'classes' => []];
				}
				$this->otherClasses[$classSession->schoolClass->course_id]['classes'][$classSession->id] =
				[
					'selected' => false,
					'post' => date('Y-m-d\TH:i'),
					'due' => date('Y-m-d\TH:i'),
					'class' => $classSession->full_name,
					'students' => [],
					'post_to_class' => true,
					'criteria' => $classSession->classCriteria->pluck('name', 'id')->toArray(),
					'criteria_id' => $classSession->classCriteria->first()?->id?? null,
					'criteria_weight' => 1,
				];
			}
		}
	}
	
	public function post()
	{
		$this->validate();
		$this->updateTemplate();
		//first, we create the Learning Demonstration
		$learningDemonstration = new LearningDemonstration();
		$learningDemonstration->person_id = $this->faculty->id;
		$learningDemonstration->type_id = $this->ld->type_id;
		$learningDemonstration->template_id = $this->ld->id;
		$learningDemonstration->year_id = Year::currentYear()->id;
		$learningDemonstration->name = $this->ld->name;
		$learningDemonstration->abbr = $this->ld->abbr;
		$learningDemonstration->demonstration = $this->ld->demonstration;
		$learningDemonstration->links =$this->ld->links;
		$learningDemonstration->questions = $this->ld->questions;
		$learningDemonstration->allow_rating = $this->ld->allow_rating;
		$learningDemonstration->online_submission = $this->ld->online_submission;
		$learningDemonstration->open_submission = $this->ld->open_submission;
		$learningDemonstration->submit_after_due = $this->ld->submit_after_due;
		$learningDemonstration->share_submissions = $this->ld->share_submissions;
		$learningDemonstration->save();
		//attach any files
		if($this->ld->workFiles()->count() > 0)
			$learningDemonstration->copyWorkFilesFrom($this->ld);
		//re-link skills with the updated rubric and weights.*/
		$linkedSkills = array_map(fn($assessment) => ['rubric' => $assessment['rubric'], 'weight' => $assessment['weight']], $this->assessments);
		array_walk($linkedSkills, fn($s) => Log::info('Creating: ' . print_r($s['rubric']->createAssessment(), true)));
		$learningDemonstration->skills()->sync($linkedSkills);
		//merge the classes arrays
		$classSessions = array_filter($this->courseClasses, fn($class) => $class['selected']);
		foreach($this->otherClasses as $courseId => $courseClasses)
		{
			$classes = array_filter($courseClasses['classes'], fn($class) => $class['selected']);
			if(count($classes) > 0)
				$classSessions = array_merge($classSessions, $classes);
		}
		//now we begin posting the LearningDemonstration, we will be posting defaults to the ClassSession model first.
		foreach($classSessions as $sessionId => $sessionInfo)
		{
			$ldSession = new LearningDemonstrationClassSession();
			$ldSession->demonstration_id = $learningDemonstration->id;
			$ldSession->session_id = $sessionId;
			$ldSession->criteria_id = $sessionInfo['criteria_id'];
			$ldSession->criteria_weight = $sessionInfo['criteria_weight'];
			$ldSession->posted_on = Carbon::createFromTimeString($sessionInfo['post'])->toDateTimeString();
			$ldSession->due_on = Carbon::createFromTimeString($sessionInfo['due'])->toDateTimeString();
			$ldSession->save();
			//next, we post the LearningDemonstrationOpportunities, which will be attached to this LearningDemonstrationClassSession
			if($sessionInfo['post_to_class'])
				$students = ClassSession::find($sessionId)->students->pluck('name', 'id')->toArray();
			else
				$students = array_filter($sessionInfo['students'], fn($student) => $student['selected']);
			foreach($students as $studentId => $studentName)
			{
				$lo = new LearningDemonstrationOpportunity();
				$lo->demonstration_session_id = $ldSession->id;
				$lo->student_id = $studentId;
				$lo->posted_on = Carbon::createFromTimeString($sessionInfo['post'])->toDateTimeString();
				$lo->due_on = Carbon::createFromTimeString($sessionInfo['due'])->toDateTimeString();
				$lo->criteria_weight = $ldSession->criteria_weight;
				$lo->save();
				foreach($linkedSkills as $skillId => $skillInfo)
				{
					$loAssessment = new LearningDemonstrationOpportunityAssessment();
					$loAssessment->opportunity_id = $lo->id;
					$loAssessment->skill_id = $skillId;
					$loAssessment->rubric = $skillInfo['rubric']->createAssessment();
					$loAssessment->weight = $skillInfo['weight'];
					$loAssessment->save();
				}
			}
		}
		//finally, send the event.
		DemonstrationPostedEvent::dispatch($learningDemonstration);
		//and redirect to the assessment page.
		return $this->redirect(route('learning.ld.assess', ['ld' => $learningDemonstration->id]), navigate: true);
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
		$this->ld->name = $this->name;
		$this->ld->abbr = $this->abbr;
		$this->ld->demonstration = $this->demonstration;
		$this->ld->links = count($this->links) > 0? array_map(fn($link) => UrlResource::hydrate($link), $this->links): [];
		$this->ld->questions = count($this->questions) > 0? array_map(fn($question) => DemonstrationQuestion::hydrate($question), $this->questions): [];
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
		$this->ld->refresh();
		$this->dispatch('template-updated');
	}

	public function toggleCoursePostStudents(int $classId)
	{
		$this->courseClasses[$classId]['post_to_class'] = !$this->courseClasses[$classId]['post_to_class'];
		if($this->courseClasses[$classId]['post_to_class'])
			$this->courseClasses[$classId]['students'] = [];
		else
		{
			$students = ClassSession::find($classId)->students;
			foreach($students as $student)
				$this->courseClasses[$classId]['students'][$student->id] = ['selected' => true, 'student' => $student->name];
		}
	}

	public function toggleOtherPostStudents(int $courseId, int $classId)
	{
		$this->otherClasses[$courseId]['classes'][$classId]['post_to_class'] = !$this->otherClasses[$courseId]['classes'][$classId]['post_to_class'];
		if($this->otherClasses[$courseId]['classes'][$classId]['post_to_class'])
			$this->otherClasses[$courseId]['classes'][$classId]['students'] = [];
		else
		{
			$students = ClassSession::find($classId)->students;
			foreach($students as $student)
				$this->otherClasses[$courseId]['classes'][$classId]['students'][$student->id] = ['selected' => true, 'student' => $student->name];
		}
	}

	public function applyDefaultPost(string $date)
	{
		foreach($this->courseClasses as $classId => $classInfo)
			$this->courseClasses[$classId]['post'] = $date;
		foreach($this->otherClasses as $courseId => $courseClasses)
			foreach($courseClasses['classes'] as $classId => $classInfo)
				$this->otherClasses[$courseId]['classes'][$classId]['post'] = $date;
	}

	public function applyDefaultDue(string $date)
	{
		foreach($this->courseClasses as $classId => $classInfo)
			$this->courseClasses[$classId]['due'] = $date;
		foreach($this->otherClasses as $courseId => $courseClasses)
			foreach($courseClasses['classes'] as $classId => $classInfo)
				$this->otherClasses[$courseId]['classes'][$classId]['due'] = $date;
	}
	
    public function render()
    {
	    return view('livewire.subject-matter.learning.learning-demonstration-poster')
		    ->extends('layouts.app', ['breadcrumb' => $this->breadcrumb])
		    ->section('content');
    }
}
