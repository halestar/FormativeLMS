<?php

namespace App\Livewire\School\ClassManagement;

use App\Enums\ClassViewer;
use App\Enums\WorkStoragesInstances;
use App\Events\Classes\NewClassAlert;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassAnnouncement;
use App\Models\SubjectMatter\Components\ClassCommunicationObject;
use App\Models\Utilities\TemporaryFiler;
use App\Notifications\Classes\ClassAlert;
use App\Notifications\Classes\NewClassStatusNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ClassAnnouncements extends Component
{
	public ClassSession $classSession;
	public Person $self;
	public bool $canManage = false;
	public bool $editing = false;
	public bool $adding = false;
	public bool $viewingAll = false;
	public ?ClassAnnouncement $editObj = null;
	#[Validate('required|min:3')]
	public string $announcementTitle = '';
	#[Validate('required|min:10')]
	public string $announcement = '';
	#[Validate('required|date_format:Y-m-d|before:postTo')]
	public string $postFrom;
	#[Validate('required|date_format:Y-m-d|after:postFrom')]
	public string $postTo;
	#[Validate('required|hex_color')]
	public string $announcementColor = '#ffffff';
	#[Validate('nullable|array')]
	public array $alsoPost = [];

	public string $classes = '';
	public string $style = '';
	public ?TemporaryFiler $filer;
	public string $refreshKey = '';
	
	public function mount(ClassSession $session)
	{
		$this->classSession = $session;
		$this->self = auth()->user();
		$this->canManage = $this->classSession->viewingAs( ClassViewer::ADMIN) || $this->classSession->viewingAs( ClassViewer::FACULTY);
		$this->postFrom = date('Y-m-d');
		$this->postTo = date('Y-m-d', strtotime('+1 week'));
		if($this->canManage)
		{
			//we might need a temporary filer, so reserve one.
			$this->filer = TemporaryFiler::getInstance(WorkStoragesInstances::ClassWork);
			$this->filer->empty();
		}
		$this->refreshKey = uniqid();
		//clear any top status notifications.
		$updated = false;
		foreach($this->self->lmsNotifications as $notification)
		{
			if($notification->data['notification_class'] == ClassAlert::class && $notification->data['session_id'] == $session->id)
			{
				$notification->delete();
				$updated = true;
			}
		}
		if($updated)
			$this->dispatch('alert-manager-read-notifications');
	}
	
	public function addAnnouncement(): void
	{
		$this->validate();
		$announcementData =
		[
			'className' => ClassAnnouncement::class,
			'value' => json_encode(
			[
				'title' => $this->announcementTitle,
				'announcement' => $this->announcement,
				'post_from' => $this->postFrom,
				'post_to' => $this->postTo,
				'color' => $this->announcementColor,
			]),
			'posted_by' => $this->self->id,
		];
		$newAnnouncement = new ClassAnnouncement();
		$newAnnouncement->session_id = $this->classSession->id;
		$newAnnouncement->fill($announcementData);
		$newAnnouncement->save();
		$this->filer->transferFiles($newAnnouncement);
		//notify everyone
		NewClassAlert::dispatch($newAnnouncement, __('subjects.school.widgets.class-announcements.action.new'));
		//do we post to others?
		foreach($this->alsoPost as $session_id)
		{
			$sessionAnnouncement = new ClassAnnouncement();
			$sessionAnnouncement->session_id = $session_id;
			$sessionAnnouncement->fill($announcementData);
			$sessionAnnouncement->save();
			$sessionAnnouncement->copyWorkFilesFrom($newAnnouncement);
			NewClassAlert::dispatch($sessionAnnouncement, __('subjects.school.widgets.class-announcements.action.new'));
		}
		$this->clearAnnouncementForm();
	}
	
	public function clearAnnouncementForm()
	{
		$this->adding = false;
		$this->editing = false;
		$this->announcementTitle = '';
		$this->announcement = '';
		$this->postFrom = date('Y-m-d');
		$this->postTo = date('Y-m-d', strtotime('+1 week'));
		$this->announcementColor = '#ffffff';
		$this->editObj = null;
		$this->refreshKey = uniqid();
	}
	
	public function setEdit(string $announcementId)
	{
		$this->editing = true;
		$this->adding = false;
		$this->editObj = ClassAnnouncement::find($announcementId);
		$this->announcementTitle = $this->editObj->title;
		$this->announcement = $this->editObj->announcement;
		$this->postFrom = $this->editObj->post_from->format('Y-m-d');
		$this->postTo = $this->editObj->post_to->format('Y-m-d');
		$this->announcementColor = $this->editObj->color;
	}

	public function updateAnnouncement()
	{
		$this->validate();
		$announcementData = json_encode(
		[
			'title' => $this->announcementTitle,
			'announcement' => $this->announcement,
			'post_from' => $this->postFrom,
			'post_to' => $this->postTo,
			'color' => $this->announcementColor,
		]);
		$this->editObj->value = $announcementData;
		$this->editObj->save();
		//notify everyone
		NewClassAlert::dispatch($this->editObj, __('subjects.school.widgets.class-announcements.action.update'));
		$this->clearAnnouncementForm();
	}
	
	public function deleteAnnouncement(string $announcementId)
	{
		$announcement = ClassAnnouncement::find($announcementId);
		if($announcement)
			$announcement->delete();

	}
	
	public function render()
	{
		if($this->viewingAll)
			$announcements = $this->classSession->communicationObjects(ClassAnnouncement::class)->get();
		else
			$announcements = $this->classSession->communicationObjects(ClassAnnouncement::class)->get()
				->filter(fn(ClassAnnouncement $a) => $a->post_to->isFuture() && $a->post_from->isPast());
		return view('livewire.school.class-management.class-announcements', compact('announcements'));
	}
}
