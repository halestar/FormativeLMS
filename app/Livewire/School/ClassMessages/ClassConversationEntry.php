<?php

namespace App\Livewire\School\ClassMessages;

use App\Classes\Settings\SchoolSettings;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassMessage;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class ClassConversationEntry extends Component
{
    public Person $self;
    public ClassSession $session;
    public StudentRecord $student;
    public bool $selected = false;
    public ?ClassMessage $latestMessage = null;
    public bool $yearMsg = true;
    public int $numUnreadMessages = 0;

    public function mount(ClassSession $session, StudentRecord $student)
    {
        $this->self = auth()->user();
        $this->session = $session;
        $this->student = $student;
        $this->latestMessage = ClassMessage::latestMessage($this->session, $student);
        $this->numUnreadMessages = ClassMessage::numUnreadMessages($this->session, $this->student, $this->self);
    }

    public function getListeners()
    {
        return
            [
                "echo-private:people.{$this->self->id},.classMessage" => 'receiveMessage',
                'class-messages-added-to-conversation' => 'newMessage',
                'class-messages-messages-read' => 'update'
            ];
    }

    public function update()
    {
        $this->latestMessage = ClassMessage::latestMessage($this->session, $this->student);
        $this->numUnreadMessages = ClassMessage::numUnreadMessages($this->session, $this->student, $this->self);
    }

    public function newMessage(ClassSession $session, StudentRecord $student)
    {
        if(!$this->selected && $session->id == $this->session->id && $student->id == $this->student->id)
            $this->update();
    }

    public function receiveMessage($event)
    {
        if(!$this->selected && $this->student->id == $event['student_id'] && $this->session->id == $event['session_id'])
            $this->update();
    }

    public function selectConversation()
    {
        if(!$this->selected)
        {
            $this->selected = true;
            $this->dispatch('class-messages-change-conversation', session: $this->session->id, student: $this->student->id);
        }
    }

    public function render()
    {
        return view('livewire.school.class-messages.class-conversation-entry');
    }
}
