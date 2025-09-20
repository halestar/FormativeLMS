<?php

namespace App\Livewire\School;

use App\Classes\ClassManagement\ClassAnnouncementsWidget;
use App\Models\SubjectMatter\ClassSession;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ClassAnnouncements extends Component
{
	public ClassAnnouncementsWidget $widget;
	public ClassSession $session;
	public string $classAnnouncementsTitle;
	public bool $canManage = false;
	public bool $adding = false;
	public bool $editing = false;
	public array $announcements = [];
	public array $otherWidgets = [];
	public array $sessionWidgets = [];
	public bool $viewingAll = false;
	public ?string $editId = null;
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
	public bool $notify = false;
	
	public function mount(ClassAnnouncementsWidget $classWidget, bool $canManage = false)
	{
		$this->widget = $classWidget;
		$this->canManage = $canManage;
		$this->postFrom = date('Y-m-d');
		$this->postTo = date('Y-m-d', strtotime('+1 week'));
		$this->announcements = $this->widget->getAnnouncements();
		$this->classAnnouncementsTitle = $this->widget->getTitle();
		//get all the other widgets that we can posts announcement to
		$sessions = ClassAnnouncementsWidget::sessionsWithWidgets();
		$this->sessionWidgets = [];
		$this->otherWidgets = [];
		foreach($sessions as $session)
		{
			$sessionWidgets = [];
			foreach($session->layout->getWidgetTypes(ClassAnnouncementsWidget::class) as $widget)
			{
				if($widget->getId() != $this->widget->getId())
				{
					$sessionWidgets[$widget->getId()] = $widget;
					$this->otherWidgets[$widget->getId()] = $widget;
				}
			}
			if(count($sessionWidgets) > 0)
			{
				$this->sessionWidgets[] =
					[
						'session' => $session,
						'widgets' => $sessionWidgets,
					];
			}
		}
		$this->session = $this->widget->getClassSession();
	}
	
	public function toggleViewAll()
	{
		$this->viewingAll = !$this->viewingAll;
		if($this->viewingAll)
			$this->announcements = $this->widget->getAllAnnouncements();
		else
			$this->announcements = $this->widget->getAnnouncements();
	}
	
	public function addAnnouncement(): void
	{
		$this->validate();
		$announcementData =
			[
				'title' => $this->announcementTitle,
				'announcement' => $this->announcement,
				'post_from' => $this->postFrom,
				'post_to' => $this->postTo,
				'color' => $this->announcementColor,
			];
		$this->widget->addAnnouncement($announcementData);
		foreach($this->alsoPost as $widgetId)
			$this->otherWidgets[$widgetId]->addAnnouncement($announcementData);
		$this->clearAnnouncementForm();
		$this->announcements = $this->widget->getAnnouncements();
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
		$this->editId = null;
		$this->dispatch('clear-editor');
	}
	
	public function setAdd()
	{
		$this->adding = true;
		$this->dispatch('init-editor');
	}
	
	public function setEdit(string $announcementId)
	{
		$this->editing = true;
		$this->adding = false;
		$this->editId = $announcementId;
		$announcement = $this->widget->getAnnouncement($announcementId);
		$this->announcementTitle = $announcement['title'];
		$this->announcement = $announcement['announcement'];
		$this->postFrom = $announcement['post_from'];
		$this->postTo = $announcement['post_to'];
		$this->announcementColor = $announcement['color'];
		$this->dispatch('init-editor');
	}
	
	public function updateAnnouncement()
	{
		$this->validate();
		$announcementData =
			[
				'id' => $this->editId,
				'title' => $this->announcementTitle,
				'announcement' => $this->announcement,
				'post_from' => $this->postFrom,
				'post_to' => $this->postTo,
				'color' => $this->announcementColor,
			];
		$this->widget->updateAnnouncement($announcementData, $this->notify);
		$this->clearAnnouncementForm();
		$this->announcements = $this->widget->getAnnouncements();
	}
	
	public function updateTitle()
	{
		$this->widget->setTitle($this->classAnnouncementsTitle);
		$this->widget->saveWidget();
	}
	
	public function deleteAnnouncement(string $announcementId)
	{
		$this->widget->removeAnnouncement($announcementId);
		$this->announcements = $this->widget->getAnnouncements();
	}
	
	public function render()
	{
		return view('livewire.school.class-announcements');
	}
}
