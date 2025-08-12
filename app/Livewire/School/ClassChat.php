<?php

namespace App\Livewire\School;

use App\Classes\Settings\SchoolSettings;
use App\Events\NewClassMessage;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassMessage;
use App\Notifications\NewClassMessageNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Livewire\WithPagination;

class ClassChat extends Component
{
    use WithPagination;

    public Person $self;
    public ClassSession $session;
    public StudentRecord $student;
    public int $fromType;
    public string $newMsg = "";
    public int $maxMessages;
	public SchoolSettings $schoolSettings;

    public function mount(ClassSession $session, StudentRecord $student)
    {
	    $this->schoolSettings = app(SchoolSettings::class);
		$this->maxMessages = $this->schoolSettings->max_msg;
        $this->session = $session;
        $this->student = $student;
        $this->self = auth()->user();
        //figure out from_type
        //first, is this a student of the class?
        if($this->self->isStudent() && $this->student->person_id == $this->self->id)
            $this->fromType = ClassMessage::FROM_STUDENT;
        elseif($this->self->isTeacher() && $this->session->isClassTeacher($this->self))
            $this->fromType = ClassMessage::FROM_TEACHER;
        elseif($this->self->isParent() && $this->self->isParentOfPerson($this->student->person))
            $this->fromType = ClassMessage::FROM_PARENT;
        else
            $this->fromType = ClassMessage::FROM_ADMIN;
        $this->updateSettings();
    }

    private function updateSettings()
    {
        //read the messages
        $this->self->prefs->set('session.' . $this->session->id . '.messages.student.' . $this->student->id .
            '.last_read', date('Y-m-d H:i:s'));
        $this->self->save();
        $this->resetPage();
        $this->dispatch('messages-loaded');
        //next, we remove all new notifications
        foreach($this->self->classMessageNotifications as $notification)
        {
            if($notification->data['misc']['session_id'] == $this->session->id &&
                $notification->data['misc']['student_id'] == $this->student->id)
                $notification->markAsRead();
        }
        //and send up a message to the notifier
        $this->dispatch('update-message-notifier-status');
    }

    public function sendMessage()
    {
        if($this->newMsg != "")
        {
            //first, we create the message
            $classMessage = new ClassMessage();
            $classMessage->session_id = $this->session->id;
            $classMessage->student_id = $this->student->id;
            $classMessage->person_id = $this->self->id;
            $classMessage->message = $this->newMsg;
            $classMessage->from_type = $this->fromType;
            $classMessage->save();
            //next gather the people to send the notification to
            if($this->fromType == ClassMessage::FROM_TEACHER)
            {
                $recipients = $this->student->person->parents;
                $recipients = $recipients->push($this->student->person);
                $recipients = $recipients->concat($this->session->teachers()->where('person_id', '!=', $this->self->id)->get());
            }
            elseif($this->fromType == ClassMessage::FROM_PARENT)
            {
                $recipients = $this->session->teachers;
                $recipients->push($this->student->person);
                $recipients = $recipients->concat($this->student->person->parents()->whereNot('people.id', $this->self->id)->get());
                $recipients = $recipients->concat($this->student->trackers);
            }
            elseif($this->fromType != ClassMessage::FROM_STUDENT)
            {
                $recipients = $this->session->teachers;
                $recipients = $recipients->concat($this->student->person->parents);
                $recipients = $recipients->concat($this->student->trackers);
            }
            else
            {
                //from admin
                $recipients = $this->student->person->parents;
                $recipients = $recipients->push($this->student->person);
                $recipients = $recipients->concat($this->session->teachers);
                $recipients = $recipients->concat($this->student->trackers()->whereNot('id', $this->self->id)->get());
            }
            Notification::send($recipients, new NewClassMessageNotification($classMessage));
            //and we also need to send the global broadcast message.
            Event::dispatch(new NewClassMessage($classMessage));
            $this->newMsg = "";
            $this->updateSettings();
        }
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
        if($this->student->id == $event['message']['student_id'] && $this->session->id == $event['message']['session_id'])
        {
            //in this case just how him the message.
            $this->resetPage();
            $this->dispatch('messages-loaded');
            //and update the reading settings.
            $this->updateSettings();
        }
    }

    public function render()
    {
		if($this->schoolSettings->year_messages == SchoolSettings::TERM)
		{
			$messages = ClassMessage::where('session_id', $this->session->id)
				->where('student_id', $this->student->id)
				->simplePaginate($this->maxMessages);
		}
		else
		{
			$messages = ClassMessage::whereIn('session_id', $this->session->schoolClass->sessions->pluck('id'))
				->where('student_id', $this->student->id)
				->simplePaginate($this->maxMessages);
		}
        return view('livewire.school.class-chat', ['messages' => $messages]);
    }
}
