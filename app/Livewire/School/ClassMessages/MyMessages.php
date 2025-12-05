<?php

namespace App\Livewire\School\ClassMessages;

use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\ClassSession;
use App\Models\Utilities\SchoolRoles;
use App\Notifications\Classes\NewClassMessageNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyMessages extends Component
{
    public Person $self;
    public array $breadcrumb;
    public array $conversations = [];
    public ?array $selectedConversation = null;
    public ClassSession $selectedSession;
    public StudentRecord $selectedStudent;
    public int $selectedStudentId;
    public string $selectedSessionId;

    public ?Collection $sessions = null;
    public ?Collection $students = null;
    public bool $viewingAsTeacher = false;
    public bool $viewingAsStudent = false;
    public bool $viewingAsParent = false;
    public bool $viewingAsAdmin = false;
	public array $roles;
	public string $selectedRole;

    public function mount()
    {
        $this->breadcrumb = [__('subjects.school.message.mine') => "#"];
        $classMessageId = null;
        if(request()->has('notification_id'))
        {
            $notification = Auth::user()
                ->notifications()
                ->where('id', request()->input('notification_id'))
                ->first();
            if($notification && $notification->type == NewClassMessageNotification::class)
                $classMessageId = $notification->data['misc']['message_id'];
        }
		$this->roles = [];
        //we figure out the conversations based on the user's role.
        $this->self = Auth::user();
        $this->sessions = new Collection();
        $this->students = new Collection();
        if($this->self->isTeacher())
	        $this->roles[SchoolRoles::$FACULTY] = 'viewingAsTeacher';
        if($this->self->isStudent())
	        $this->roles[SchoolRoles::$STUDENT] = 'viewingAsStudent';
        if($this->self->isParent())
	        $this->roles[SchoolRoles::$PARENT] = 'viewingAsParent';
		if($this->self->can('school.tracker'))
			$this->roles[SchoolRoles::$ADMIN] = 'viewingAsAdmin';
		if(count($this->roles) == 0)
			abort(403);
		$this->selectedRole = array_key_first($this->roles);
        $this->updateRole();
    }

	public function updateRole()
	{
		if(!isset($this->roles[$this->selectedRole]))
			abort(403);
		$this->viewingAsTeacher = false;
		$this->viewingAsStudent = false;
		$this->viewingAsParent = false;
		$this->viewingAsAdmin = false;
		$viewVar = $this->roles[$this->selectedRole];
		$this->$viewVar = true;
		$this->updateViewingAs();
	}

	public function updateViewingAs()
	{
		if($this->viewingAsTeacher)
		{
			$this->sessions = $this->self->currentClassSessions;
			$this->selectedSession = $this->sessions->first();
		}
		elseif($this->viewingAsStudent)
		{
			$this->sessions = new Collection();
			$this->students = new Collection();
		}
		elseif($this->viewingAsParent)
		{
			$this->students = $this->self->currentChildStudents();
			$this->selectedStudent = $this->students->first();
			$this->selectedStudentId = $this->selectedStudent->id;
		}
		elseif($this->viewingAsAdmin)
		{
			$this->students = $this->self->studentTrackee->sortBy('person.name');
			$this->selectedStudent = $this->students->first();
			$this->selectedStudentId = $this->selectedStudent->id;
		}
		$this->updateConversations();
	}

    public function updateConversations()
    {
        $this->conversations = [];
        if($this->viewingAsTeacher)
        {
            $this->students = $this->selectedSession->students;
            foreach($this->students as $student)
                $this->conversations[] = ['student' => $student, 'session' => $this->selectedSession];
        }
        elseif($this->viewingAsStudent)
        {
            $student = $this->self->student();
            $this->sessions = $student?->classSessions ?? new Collection();
            foreach($this->sessions as $session)
                $this->conversations[] = ['student' => $student, 'session' => $session];
        }
        elseif($this->viewingAsParent)
        {
	        $this->sessions = $this->selectedStudent?->classSessions ?? new Collection();
            foreach($this->sessions as $session)
                $this->conversations[] = ['student' => $this->selectedStudent, 'session' => $session];
        }
        elseif($this->viewingAsAdmin)
        {
	        $this->sessions = $this->selectedStudent->classSessions ?? new Collection();
	        foreach($this->sessions as $session)
		        $this->conversations[] = ['student' => $this->selectedStudent, 'session' => $session];
        }
        if(count($this->conversations) > 0)
        {
            $this->selectedConversation = $this->conversations[0];
            $this->selectedStudent = $this->selectedConversation['student'];
            $this->selectedSession = $this->selectedConversation['session'];
            $this->selectedStudentId = $this->selectedStudent->id;
            $this->selectedSessionId = $this->selectedSession->id;
        }
    }

    public function setSession()
    {
        $this->selectedSession = $this->sessions->where('id', $this->selectedSessionId)
            ->first();
        $this->updateConversations();
        $this->dispatch('class-messages-change-conversation', session: $this->selectedSession->id, student: $this->selectedStudent->id);
    }

    public function setStudent()
    {
        $this->selectedStudent = $this->students->where('id', $this->selectedStudentId)
            ->first();
        $this->updateConversations();
        $this->dispatch('class-messages-change-conversation', session: $this->selectedSession->id, student: $this->selectedStudent->id);
    }

    public function render()
    {
        return view('livewire.school.class-messages.my-messages')
            ->extends('layouts.app', ['breadcrumb' => $this->breadcrumb])
            ->section('content');
    }
}
