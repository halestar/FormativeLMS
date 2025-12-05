<?php

namespace App\Livewire\School\ClassManagement;

use App\Enums\ClassViewer;
use App\Events\Classes\NewClassStatusEvent;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassStatus;
use App\Notifications\Classes\NewClassStatusNotification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TopAnnouncement extends Component
{
	public ClassStatus $announcement;
	public Person $self;
    public bool $canManage;
	public ?Collection $otherClasses = null;
	public bool $editing = false;
	#[Validate('nullable')]
	public string $announcementText = '';
	#[Validate('required|hex_color')]
	public string $announcementColor = '#ffffff';
	#[Validate('required|date_format:Y-m-d')]
	public string $announcementExpiry;
	public array $alsoPost = [];
	public string $classes = '';
	public string $style = '';

	
	public function mount(ClassSession $session)
	{
		$this->self = auth()->user();
		$this->announcement = $session->classManager->getClassLayoutManager($session)->getTopAnnouncement();
        $this->canManage = $this->announcement->classSession->viewingAs(ClassViewer::FACULTY) ||
            $this->announcement->classSession->viewingAs(ClassViewer::ADMIN);
        if($this->canManage)
            $this->otherClasses = $this->self->currentClassSessions()->where('class_sessions.id', '<>', $this->announcement->session_id)->get();
		//clear any top status notifications.
		$updated = false;
		foreach($this->self->lmsNotifications as $notification)
		{
			if($notification->data['notification_class'] == NewClassStatusNotification::class && $notification->data['session_id'] == $session->id)
			{
				$notification->delete();
				$updated = true;
			}
		}
		if($updated)
			$this->dispatch('alert-manager-read-notifications');
	}
	
	public function edit()
	{
		$this->editing = true;
		$this->announcementText = $this->announcement->announcement ?? '';
		$this->announcementColor = $this->announcement->color;
		$this->announcementExpiry = $this->announcement->expiry->format('Y-m-d');
	}
	
	public function saveAnnouncement()
	{
		$this->validate();
		$this->announcement->announcement = $this->announcementText;
		$this->announcement->color = $this->announcementColor;
		$this->announcement->expiry = Carbon::parse($this->announcementExpiry);
		$this->editing = false;
		$this->announcement->save();
        NewClassStatusEvent::dispatch($this->announcement);
		foreach($this->alsoPost as $session_id)
		{
			$announcement = ClassStatus::firstOrCreate(
                ['session_id' => $session_id, 'className' => ClassStatus::class],
                ['posted_by' => Auth::user()->id]);
            $announcement->announcement = $this->announcementText;
            $announcement->color = $this->announcementColor;
            $announcement->expiry = Carbon::parse($this->announcementExpiry);
            $announcement->save();
            NewClassStatusEvent::dispatch($announcement);
		}
	}
	
	public function cancel()
	{
		$this->editing = false;
	}
	
	public function render()
	{
		return view('livewire.school.class-management.top-announcement');
	}
	
	protected function messages()
	{
		return [
			'announcementText' => __('errors.top-announcement.text'),
			'announcementColor' => __('errors.top-announcement.color'),
			'announcementExpiry' => __('errors.top-announcement.expires'),
		];
	}
}
