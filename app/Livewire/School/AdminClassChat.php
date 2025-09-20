<?php

namespace App\Livewire\School;

use App\Classes\Settings\SchoolSettings;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassMessage;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class AdminClassChat extends Component
{
	public Collection $sessions;
	public ?ClassSession $session;
	//Roster Collection
	public Collection $students;
	//Selected Roster Element
	public ?StudentRecord $selectedStudent = null;
	public Person $self;
	public string $size = "";
	public array $unreadMessages = [];
	public array $latestMessage = [];
	public SchoolSettings $schoolSettings;
	public int $selectedStudentId;
	
	public function mount(int $selectedSessionId = null, string $size = "", int $selectedStudentId = null)
	{
		$this->schoolSettings = app(SchoolSettings::class);
		$selectedSession = null;
		if($selectedSessionId)
			$selectedSession = ClassSession::find($selectedSessionId);
		$this->self = auth()->user();
		$this->students = $this->self->studentTrackee->sortBy('person.name');
		$this->session = null;
		if($selectedStudentId)
			$this->selectedStudent = StudentRecord::find($selectedStudentId);
		if(!$this->selectedStudent)
			$this->selectedStudent = $this->students->first();
		if($this->selectedStudent)
			$this->selectedStudentId = $this->selectedStudent->id;
		switch($size)
		{
			case "lg":
				$this->size = "-lg";
				break;
			case "sm":
				$this->size = "-sm";
				break;
			default:
				$this->size = "";
		}
		$this->setupStudent($selectedSession);
	}
	
	private function setupStudent(ClassSession $selectedSession = null)
	{
		$this->sessions = $this->selectedStudent->classSessions->sortBy('name');
		$this->session = $selectedSession;
		if($this->session)
			$this->updateSettings();
		//collect the latest messages
		$this->unreadMessages = [];
		foreach($this->sessions as $session)
		{
			if($this->schoolSettings->year_messages == SchoolSettings::TERM)
				$this->unreadMessages[$session->id] = ClassMessage::numUnreadMessages($session, $this->selectedStudent,
					$this->self);
			else
				$this->unreadMessages[$session->id] = ClassMessage::numUnreadClassMessages($session,
					$this->selectedStudent, $this->self);
		}
		$this->latestMessage = [];
		foreach($this->sessions as $session)
			$this->latestMessage[$session->id] = ClassMessage::latestMessage($session, $this->selectedStudent);
	}
	
	private function updateSettings()
	{
		//read the messages
		$this->self->prefs->set('session.' . $this->session->id . '.messages.student.' . $this->selectedStudent->id .
			'.last_read', date('Y-m-d H:i:s'));
		$this->self->save();
		$this->unreadMessages[$this->session->id] = 0;
	}
	
	#[On('change-student')]
	public function setStudent(int $sessionId = null, int $studentId = null)
	{
		if($studentId)
			$this->selectedStudent = StudentRecord::find($studentId);
		else
			$this->selectedStudent = $this->students->where('id', $this->selectedStudentId)
			                                        ->first();
		if(!$this->selectedStudent)
			$this->selectedStudent = $this->students->first();
		if($this->selectedStudent)
			$this->selectedStudentId = $this->selectedStudent->id;
		$session = null;
		if($sessionId)
			$session = StudentRecord::find($sessionId);
		$this->setupStudent($session);
	}
	
	public function setSession(int $sessionId)
	{
		$this->session = ClassSession::find($sessionId);
		if($this->session)
			$this->updateSettings();
	}
	
	public function getListeners()
	{
		return
			[
				"echo-private:people.{$this->self->id},.newClassMessage" => 'receiveMessage',
			];
	}
	
	public function receiveMessage($event)
	{
		if($this->students->contains('id', $event['message']['student_id']) &&
			$this->sessions->contains('id', $event['message']['session_id']))
		{
			//are we currently viewing this student?
			if($this->session && $this->session->id == $event['message']['session_id'])
			{
				//update the reading settings.
				$this->updateSettings();
			}
			else
			{
				//in this case, we just make the bubble come up.
				$this->unreadMessages[$event['message']['session_id']]++;
				$this->latestMessage[$event['message']['session_id']] = ClassMessage::find($event['message']['id']);
			}
		}
	}
	
	
	public function render()
	{
		return view('livewire.school.admin-class-chat');
	}
}
