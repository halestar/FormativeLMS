<?php

namespace App\Livewire\School;

use App\Classes\Settings\SchoolSettings;
use App\Models\Locations\Term;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassMessage;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class StudentClassChat extends Component
{

    //Roster Collection
    public Collection $sessions;

    //Selected Roster Element
    public StudentRecord $student;

    //Selected Roster Element
    public ?ClassSession $selectedSession = null;

    public Person $self;
    public string $size = "";
    public array $unreadMessages = [];
    public array $latestMessage = [];
    public SchoolSettings $schoolSettings;


    public function mount(string $size = "", int $selectedSessionId = null)
    {
        $this->schoolSettings = app(SchoolSettings::class);
        $selectedSession = null;
        if($selectedSessionId)
            $selectedSession = ClassSession::find($selectedSessionId);
        $this->self = auth()->user();
        $this->student = $this->self->student();
        switch($size)
        {
            case "lg": $this->size = "-lg"; break;
            case "sm": $this->size = "-sm"; break;
            default: $this->size = "";
        }
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
        {
            if($this->schoolSettings->year_messages == SchoolSettings::TERM)
                $this->unreadMessages[$session->id] = ClassMessage::numUnreadMessages($session, $this->student, $this->self);
            else
                $this->unreadMessages[$session->id] = ClassMessage::numUnreadClassMessages($session, $this->student, $this->self);
        }
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

    #[On('change-term')]
    public function updateTerm(int $termId, int $sessionId = null)
    {
        $term = Term::find($termId);
        $session = null;
        if($sessionId)
            $session = ClassSession::find($sessionId);
        if($term)
        {
            $this->student = $this->self->studentInTerm($term);
            $this->setupStudent($session);
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
        if($this->sessions->contains('id', $event['message']['session_id']))
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
        return view('livewire.school.student-class-chat');
    }
}
