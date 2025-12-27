<?php

namespace App\Livewire\SubjectMatter\Learning;

use App\Classes\Learning\DemonstrationQuestion;
use App\Classes\Learning\UrlResource;
use App\Enums\SystemLogType;
use App\Events\Learning\DemonstrationDeletedEvent;
use App\Events\Learning\DemonstrationUpdatedEvent;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Learning\LearningDemonstration;
use App\Models\SubjectMatter\Learning\LearningDemonstrationOpportunity;
use App\Models\SubjectMatter\Learning\LearningDemonstrationTemplate;
use App\Notifications\Learning\LearningDemonstrationDeletedNotification;
use App\Notifications\Learning\LearningDemonstrationPostedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\On;
use Livewire\Component;

class LearningDemonstrationEditor extends Component
{
	public array $breadcrumb;
	public Person $faculty;
	public ?LearningDemonstration $ld;
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
					'skill_id' => $skill->id,
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
					'to_post' => isset($opportunities[$student->id]),
					'post' => isset($opportunities[$student->id]) ? $opportunities[$student->id]->posted_on->format('Y-m-d\TH:i') :
						$session->session->posted_on->format('Y-m-d\TH:i'),
					'due' => isset($opportunities[$student->id]) ? $opportunities[$student->id]->due_on->format('Y-m-d\TH:i') :
						$session->session->due_on->format('Y-m-d\TH:i'),
					'completed' => isset($opportunities[$student->id]) && $opportunities[$student->id]->completed,
					'submitted' => isset($opportunities[$student->id]) && $opportunities[$student->id]->submitted_on,
					'criteria_weight' => isset($opportunities[$student->id]) ? $opportunities[$student->id]->criteria_weight : $session->session->criteria_weight,
					'remove_post' => false,
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

	public function applyDefaultPost(string $date)
	{
		foreach($this->postedClasses as $classId => $classInfo)
		{
			$this->postedClasses[$classId]['post'] = $date;
			foreach($this->postedClasses[$classId]['students'] as $studentId => $studentInfo)
				$this->postedClasses[$classId]['students'][$studentId]['post'] = $date;
		}
	}

	public function applyDefaultDue(string $date)
	{
		foreach($this->postedClasses as $classId => $classInfo)
		{
			$this->postedClasses[$classId]['due'] = $date;
			foreach($this->postedClasses[$classId]['students'] as $studentId => $studentInfo)
				$this->postedClasses[$classId]['students'][$studentId]['due'] = $date;
		}
	}

	public function updateLearningDemonstration()
	{
		$this->validate();
		$log = [];
		if($this->name != $this->ld->name)
		{
			$log[] = __('logs.ld.update.name', ['old' => $this->ld->name, 'new' => $this->name]);
			$this->ld->name = $this->name;
		}
		if($this->abbr != $this->ld->abbr)
		{
			$log[] = __('logs.ld.update.abbr', ['old' => $this->ld->abbr, 'new' => $this->abbr]);
			$this->ld->abbr = $this->abbr;
		}
		if($this->demonstration != $this->ld->demonstration)
		{
			$log[] = __('logs.ld.update.demonstration', ['old' => $this->ld->demonstration, 'new' => $this->demonstration]);
			$this->ld->demonstration = $this->demonstration;
		}
		//are there any file updates?
		foreach($this->ld->workFiles as $file)
			if($file->updated_at > $this->ld->updated_at)
				$log[] = __('logs.ld.update.file', ['file' => $file->name]);

		//skills - detachments and rubric changes first
		$skillSync = [];
		foreach($this->ld->skills as $skill)
		{
			//detaching this skill
			if(!isset($this->assessments[$skill->id]))
				$log[] = __('logs.ld.update.skill.detach', ['skill' => $skill->prettyName()]);
			else
			{
				//are we changing the weight?
				if($this->assessments[$skill->id]['weight'] != $skill->assessment->weight)
					$log[] = __('logs.ld.update.skill.weight', ['skill' => $skill->prettyName()]);
				//are we changing the rubric?
				if($this->assessments[$skill->id]['rubric']->numCriteria() != $skill->rubric->numCriteria())
					$log[] = __('logs.ld.update.skill.rubric', ['skill' => $skill->prettyName()]);
				//add it to the sync array
				$skillSync[$skill->id] =
				[
					'rubric' => $this->assessments[$skill->id]['rubric'],
					'weight' => $this->assessments[$skill->id]['weight'],
				];
			}
		}
		//attachments are next
		foreach($this->assessments as $skillId => $assessment)
		{
			if(str_starts_with($assessment['id'], 'new_'))
			{
				$log[] = __('logs.ld.update.skill.attach', ['skill' => $assessment['name']]);
				$skillSync[$skillId] =
				[
					'rubric' => $assessment['rubric'],
					'weight' => $assessment['weight'],
				];
			}
		}
		//and we sync
		$this->ld->skills()->sync($skillSync);

		//links are next
		if(json_encode($this->links) != json_encode($this->ld->links))
		{
			$log[] = __('logs.ld.update.links');
			$this->ld->links = count($this->links) > 0? array_map(fn($link) => UrlResource::hydrate($link), $this->links): [];
		}

		//questions
		if(json_encode($this->questions) != json_encode($this->ld->questions))
		{
			$log[] = __('logs.ld.update.questions');
			$this->ld->questions = count($this->questions) > 0? array_map(fn($question) => DemonstrationQuestion::hydrate($question), $this->questions): [];
		}

		//posting options
		if($this->allow_rating != $this->ld->allow_rating)
		{
			$log[] = $this->allow_rating ? __('logs.ld.update.allow_rating.on') : __('logs.ld.update.allow_rating.off');
			$this->ld->allow_rating = $this->allow_rating;
		}
		if($this->online_submission != $this->ld->online_submission)
		{
			$log[] = $this->online_submission ? __('logs.ld.update.online_submission.on') : __('logs.ld.update.online_submission.off');
			$this->ld->online_submission = $this->online_submission;
		}
		if($this->open_submission != $this->ld->open_submission)
		{
			$log[] = $this->open_submission ? __('logs.ld.update.open_submission.on') : __('logs.ld.update.open_submission.off');
			$this->ld->open_submission = $this->open_submission;
		}
		if($this->submit_after_due != $this->ld->submit_after_due)
		{
			$log[] = $this->submit_after_due ? __('logs.ld.update.submit_after_due.on') : __('logs.ld.update.submit_after_due.off');
			$this->ld->submit_after_due = $this->submit_after_due;
		}
		if($this->share_submissions != $this->ld->share_submissions)
		{
			$log[] = $this->share_submissions ? __('logs.ld.update.share_submissions.on') : __('logs.ld.update.share_submissions.off');
			$this->ld->share_submissions = $this->share_submissions;
		}

		//next, we need to update the posted classes.
		foreach($this->postedClasses as $sessionId => $classInfo)
		{
			$session = ClassSession::find($sessionId);
			$dSession = $this->ld->demonstrationSession($session);
			//make the assessment syncyng array
			$assessmentSync = [];
			foreach ($this->assessments as $skillId => $skillInfo)
			{
				$assessmentSync[$skillId] =
				[
					'rubric' => $skillInfo['rubric']->createAssessment(),
					'weight' => $skillInfo['weight'],
				];
			}
			//before we update the session, we need to update the oppotunities.
			foreach ($classInfo['students'] as $studentId => $studentInfo)
			{
				//is this opportunity already posted?
				if ($studentInfo['posted'])
				{
					//it is, are we removing it?
					if ($studentInfo['remove_post'])
					{
						//yes, we are removing it.
						$student = StudentRecord::find($studentId);
						$opportunity = $dSession->opportunity($student);
						$opportunity->delete();
						//notify the student
						$student->person->notify(new LearningDemonstrationDeletedNotification($session, $this->ld->name));
						Notification::send($student->person->parents,
							new LearningDemonstrationDeletedNotification($session, $this->ld->name));
					}
					else
					{
						//in this case, we are updating the opportunity.
						$opportunity = $dSession->opportunity($studentId);
						$opportunity->posted_on = $studentInfo['post'];
						$opportunity->due_on = $studentInfo['due'];
						$opportunity->criteria_weight = $studentInfo['criteria_weight'];
						$opportunity->save();
						$opportunity->skills()->sync($assessmentSync);
					}
				}
				elseif ($studentInfo['to_post'])
				{
					//in this case, we are creating a new opportunity.
					$student = StudentRecord::find($studentId);
					$opportunity = new LearningDemonstrationOpportunity();
					$opportunity->demonstration_session_id = $dSession->id;
					$opportunity->student_id = $studentId;
					$opportunity->posted_on = $studentInfo['post'];
					$opportunity->due_on = $studentInfo['due'];
					$opportunity->criteria_weight = $studentInfo['criteria_weight'];
					$opportunity->save();
					$opportunity->skills()->sync($assessmentSync);
					//and notify the student
					$student->person->notify(new LearningDemonstrationPostedNotification($opportunity));
					Notification::send($student->person->parents,
						new LearningDemonstrationPostedNotification($opportunity));
				}
			}
			if ($dSession->opportunities()->count() == 0)
			{
				//in this case, we need to delete the session.
				$dSession->delete();
			}
			else
			{
				//otherwise, we just update the session.
				$dSession->criteria_id = $classInfo['criteria_id'];
				$dSession->criteria_weight = $classInfo['criteria_weight'];
				$dSession->posted_on = $classInfo['post'];
				$dSession->due_on = $classInfo['due'];
				$dSession->save();
			}
		}
		//do we have any dsessions?
		if($this->ld->demonstrationSessions()->count() == 0)
		{
			//delete the LD
			defer(fn() => $this->ld->delete());
		}
		else
		{
			//we only need to save the ld if we have at least one change
			if (count($log) > 0)
			{
				$this->ld->save();
				$this->ld->appendSystemLog(SystemLogType::LearningDemonstration, implode("\n", $log));
				//and notify people.
				DemonstrationUpdatedEvent::dispatch($this->ld);
			}
		}
		$this->redirect(route('subjects.school.classes.show', $this->classSession));
	}

	public function deleteLearningDemonstration()
	{
		//we need to delete each opportunity individually to trigger the correct events and send the notifications.
		foreach($this->ld->demonstrationSessions as $demonstrationSession)
		{
			$students = [];
			foreach($demonstrationSession->opportunities as $opportunity)
			{
				$students[] = $opportunity->student;
				$opportunity->delete();
			}
			$session = $demonstrationSession->classSession;
			$demonstrationSession->delete();
			DemonstrationDeletedEvent::dispatch($session, $students, $this->ld->name);
		}
		defer(fn() => $this->ld->delete());
		$this->redirect(route('subjects.school.classes.show', $this->classSession));
	}

	public function render()
    {
        return view('livewire.subject-matter.learning.learning-demonstration-editor')
	        ->extends('layouts.app', ['breadcrumb' => $this->breadcrumb])
	        ->section('content');
    }
}
