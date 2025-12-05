<?php

namespace App\Livewire\School\ClassMessages;

use App\Events\Classes\NewClassMessage;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class MessageSender extends Component
{
    #[Validate('required|min:1')]
    public string $newMsg = "";
    public Person $self;
    public ?ClassSession $session;
    public ?StudentRecord $student;

    public function mount()
    {
        $this->self = auth()->user();
    }

    public function sendMessage()
    {
		if($this->session && $this->student)
		{
			$this->validate();
			//first, we create the message
			$classMessage = new ClassMessage();
			$classMessage->session_id = $this->session->id;
			$classMessage->student_id = $this->student->id;
			$classMessage->person_id = $this->self->id;
			$classMessage->message = $this->newMsg;
			$classMessage->from_type = $this->self->classViewRole($this->session);
			$classMessage->save();
			Event::dispatch(new NewClassMessage($classMessage));
			//and we also need to send the global broadcast message.
			$this->dispatch('class-messages-added-to-conversation', session: $this->session->id,
				student: $this->student->id);
		}
	    $this->newMsg = "";
    }

    #[On('class-messages-change-conversation')]
    public function changeConversation(ClassSession $session, StudentRecord $student)
    {
        if($session->hasConversationAccess($student))
        {
            $this->session = $session;
            $this->student = $student;
        }
    }

    public function render()
    {
        return view('livewire.school.class-messages.message-sender');
    }
}
