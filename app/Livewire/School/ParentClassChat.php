<?php

namespace App\Livewire\School;

use App\Models\Locations\Term;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassMessage;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class ParentClassChat extends Component
{

    public ?StudentRecord $student;

    //Roster Collection
    public Collection $sessions;
    public Collection $children;

    //Selected Roster Element
    public ?ClassSession $selectedSession = null;

    public Person $self;
    public string $size = "";
    public array $unreadMessages = [];
    public array $latestMessage = [];
    public int $selectedStudentId;
    public function mount(string $size = "", int $selectedSessionId = null, int $selectedStudentId = null)
    {
        //find the valid children.
        $selectedSession = null;
        if($selectedSessionId)
            $selectedSession = ClassSession::find($selectedSessionId);
        $this->self = auth()->user();
        $this->children = $this->self->currentChildStudents();
        $this->student = null;
        if($selectedStudentId)
            $this->student = $this->children->where('id', $selectedStudentId)->first();
        if(!$this->student)
            $this->student = $this->children->first();
        if($this->student)
            $this->selectedStudentId = $this->student->id;
        switch($size)
        {
            case "lg": $this->size = "-lg"; break;
            case "sm": $this->size = "-sm"; break;
            default: $this->size = "";
        }
        if($this->student)
            $this->setupStudent($selectedSession);
    }

    private function setupStudent(ClassSession $selectedSession = null)
    {
        $this->sessions = $this->student->classSessions;
        $this->selectedSession = $selectedSession;
        if($this->selectedSession)
            $this->updateSettings();
        $this->unreadMessages = [];
        foreach($this->sessions as $session)
            $this->unreadMessages[$session->id] = ClassMessage::numUnreadMessages($session, $this->student, $this->self);
        $this->latestMessage = [];
        foreach($this->sessions as $session)
            $this->latestMessage[$session->id] = ClassMessage::latestMessage($session, $this->student);
    }

    private function updateSettings()
    {
        //read the messages
        $this->self->prefs->set('session.' . $this->selectedSession->id . '.messages.student.' . $this->student->id .
            '.last_read', date('Y-m-d H:i:s'));
        $this->self->save();
        $this->unreadMessages[$this->selectedSession->id] = 0;
    }

    #[On('change-student-term')]
    public function updateStudentTerm(int $studentId, int $termId, int $sessionId = null)
    {
        $term = Term::find($termId);
        $student = StudentRecord::find($studentId);
        $session = null;
        if($sessionId)
            $session = ClassSession::find($sessionId);
        if($term && $student)
        {
            $this->term = $term;
            $this->student = $student;
            $this->setupTerm($session);
        }
    }

    public function setSession(int $sessionId)
    {
        $this->selectedSession = ClassSession::find($sessionId);
        if($this->selectedSession)
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
        if($this->sessions->contains('id', $event['message']['session_id']) &&
            $this->student->id == $event['message']['student_id'])
        {
            if($this->selectedSession && $this->selectedSession->id == $event['message']['session_id'])
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
        return view('livewire.school.parent-class-chat');
    }
}
