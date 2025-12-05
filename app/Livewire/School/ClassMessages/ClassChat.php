<?php

namespace App\Livewire\School\ClassMessages;

use App\Classes\Settings\SchoolSettings;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ClassChat extends Component
{
	use WithPagination;
	
	public Person $self;
	public ?ClassSession $session = null;
	public ?StudentRecord $student = null;
	public int $maxMessages;
    public bool $yearMsg = true;
    public string $height = "800px";
    public string $width = "800px";
    public string $classes = "";
    public string $style = "";

	
	public function mount(SchoolSettings $settings)
	{
		$this->yearMsg = ($settings->year_messages == SchoolSettings::YEAR);
		$this->maxMessages = $settings->max_msg;
		$this->self = auth()->user();
		if($this->session && $this->student)
			$this->updateSettings();
	}
	
	private function updateSettings()
	{
		//read the messages
        $pref = 'session.' . $this->session->id . '.messages.student.' . $this->student->id . '.last_read';
		$this->self->setPreference($pref, date('Y-m-d H:i:s'));
		$this->self->save();
		//clear any notifications that led us here.
		$notifications = $this->self->classMessageNotifications;
		$deleted = false;
		foreach($notifications as $notification)
		{
			if($notification->data['session_id'] == $this->session->id &&
				$notification->data['student_id'] == $this->student->id)
			{
				$notification->delete();
				$deleted = true;
			}
		}
		if($deleted)
			$this->dispatch('message-notifications-cleared');

		$this->resetPage();
		//next, we remove all new notifications
		foreach($this->self->classMessageNotifications as $notification)
		{
			if($notification->data['session_id'] == $this->session->id &&
				$notification->data['student_id'] == $this->student->id)
				$notification->markAsRead();
		}
		//and send up a message to the notifier
		$this->dispatch('class-messages-messages-read', session: $this->session, student: $this->student);
	}

    public function changeConversation(ClassSession $session, StudentRecord $student)
    {
        if($session->hasConversationAccess($student))
        {
            $this->session = $session;
            $this->student = $student;
            $this->updateSettings();
        }
    }
	
	public function getListeners()
	{
		return
			[
				"echo-private:people.{$this->self->id},.classMessage" => 'receiveMessage',
                'class-messages-change-conversation' => 'changeConversation',
                'class-messages-added-to-conversation' => 'newMessage',
			];
	}
	
	public function receiveMessage($event)
	{
		if($this->student && $this->session && $this->student->id == $event['student_id'] && $this->session->id == $event['session_id'])
		{
			//and update the reading settings.
			$this->updateSettings();
		}
	}

    public function newMessage(ClassSession $session, StudentRecord $student)
    {
        if($this->session && $this->student && $this->session->id == $session->id && $this->student->id == $student->id)
            $this->updateSettings();
    }
	
	public function render()
	{
		if($this->session && $this->student)
		{
			if ($this->yearMsg)
			{
				$messages = ClassMessage::where('session_id', $this->session->id)
					->where('student_id', $this->student->id)
					->simplePaginate($this->maxMessages);
			} else
			{
				$messages = ClassMessage::whereIn('session_id', $this->session->schoolClass->sessions->pluck('id'))
					->where('student_id', $this->student->id)
					->simplePaginate($this->maxMessages);
			}
		}
		else
			$messages = new Collection();
		return view('livewire.school.class-messages.class-chat', ['messages' => $messages]);
	}
}
