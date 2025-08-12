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

class FacultyClassChat extends Component
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
    public int $selectedSessionId;

    public function mount(SchoolSettings $settings, int $selectedSessionId = null, string $size = "", int $selectedStudentId = null)
    {
        $this->schoolSettings = $settings;
        $selectedStudent = null;
        if($selectedStudentId)
            $selectedStudent = StudentRecord::find($selectedStudentId);
        $this->self = auth()->user();
        $this->sessions = $this->self->currentClassSessions->sortBy('name');
        $this->session = null;
        if($selectedSessionId)
            $this->session = ClassSession::find($selectedSessionId);
        if(!$this->session)
            $this->session = $this->sessions->first();
        if($this->session)
            $this->selectedSessionId = $this->session->id;
        switch($size)
        {
            case "lg": $this->size = "-lg"; break;
            case "sm": $this->size = "-sm"; break;
            default: $this->size = "";
        }
        $this->setupSession($selectedStudent);
    }


    private function setupSession(StudentRecord $selectedStudent = null)
    {
        $this->students = $this->session->students;
        $this->selectedStudent = $selectedStudent;
        if($this->selectedStudent)
            $this->updateSettings();
        //collect the latest messages
        $this->unreadMessages = [];
        foreach($this->students as $student)
        {
            if($this->schoolSettings->year_messages == SchoolSettings::TERM)
                $this->unreadMessages[$student->id] = ClassMessage::numUnreadMessages($this->session, $student, $this->self);
            else
                $this->unreadMessages[$student->id] = ClassMessage::numUnreadClassMessages($this->session, $student, $this->self);
        }
        $this->latestMessage = [];
        foreach($this->students as $student)
            $this->latestMessage[$student->id] = ClassMessage::latestMessage($this->session, $student);
    }

    private function updateSettings()
    {
        //read the messages
        $this->self->prefs->set('session.' . $this->session->id . '.messages.student.' . $this->selectedStudent->id .
            '.last_read', date('Y-m-d H:i:s'));
        $this->self->save();
        $this->unreadMessages[$this->selectedStudent->id] = 0;
    }


    #[On('change-session')]
    public function setSession(int $sessionId = null, int $studentId = null)
    {
        if($sessionId)
            $this->session = ClassSession::find($sessionId);
        else
            $this->session = $this->sessions->where('id', $this->selectedSessionId)->first();
        if(!$this->session)
            $this->session = $this->sessions->first();
        if($this->session)
            $this->selectedSessionId = $this->session->id;
        $student = null;
        if($studentId)
            $student = StudentRecord::find($studentId);
        $this->setupSession($student);
    }

    public function setStudent(int $studentId)
    {
        $this->selectedStudent = StudentRecord::find($studentId);
        if($this->selectedStudent)
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
        if($this->students->contains('id', $event['message']['student_id']))
        {
            //are we currently viewing this student?
            if($this->selectedStudent && $this->selectedStudent->id == $event['message']['student_id'])
            {
                //update the reading settings.
                $this->updateSettings();
            }
            else
            {
                //in this case, we just make the bubble come up.
                $this->unreadMessages[$event['message']['student_id']]++;
                $this->latestMessage[$event['message']['student_id']] = ClassMessage::find($event['message']['id']);
            }
        }
    }

    public function render()
    {
        return view('livewire.school.faculty-class-chat');
    }
}
