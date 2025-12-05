<?php

namespace App\Livewire\School\ClassManagement;

use App\Enums\ClassViewer;
use App\Events\Classes\NewClassAlert;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassAnnouncement;
use App\Models\SubjectMatter\Components\ClassLink;
use App\Notifications\Classes\ClassAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ClassLinks extends Component
{
	public ClassSession $classSession;
	public Person $self;
	public bool $canManage = false;
	public bool $adding = false;
	public bool $editing = false;
	public ?ClassLink $editObj = null;
	#[Validate('required|min:3')]
	public string $linkText = '';
	#[Validate('required|url')]
	public string $linkUrl = '';
	public array $alsoPost = [];

	public string $classes = '';
	public string $style = '';
	
	public function mount(ClassSession $session, bool $canManage = false)
	{
		$this->classSession = $session;
		$this->self = auth()->user();
		$this->canManage = $this->classSession->viewingAs( ClassViewer::ADMIN) || $this->classSession->viewingAs( ClassViewer::FACULTY);
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
	
	public function setEdit(string $linkId)
	{
		$this->editing = true;
		$this->adding = false;
		$this->editObj = ClassLink::find($linkId);
		$this->linkText = $this->editObj->title;
		$this->linkUrl = $this->editObj->url;
	}
	
	public function addLink(): void
	{
		$this->validate();
		$linkData =
			[
				'className' => ClassLink::class,
				'value' => json_encode(
					[
						'title' => $this->linkText,
						'url' => $this->linkUrl,
					]),
				'posted_by' => $this->self->id,
			];
		$newLink = new ClassLink();
		$newLink->session_id = $this->classSession->id;
		$newLink->fill($linkData);
		$newLink->save();
		//notify everyone
		NewClassAlert::dispatch($newLink, __('subjects.school.widgets.class-links.action.new'));
		//do we post to others?
		foreach($this->alsoPost as $session_id)
		{
			$sessionLink = new ClassLink();
			$sessionLink->session_id = $session_id;
			$sessionLink->fill($linkData);
			$sessionLink->save();
			NewClassAlert::dispatch($sessionLink, __('subjects.school.widgets.class-links.action.new'));
		}
		$this->clearLinkForm();
	}
	
	public function clearLinkForm()
	{
		$this->adding = false;
		$this->editing = false;
		$this->linkText = '';
		$this->linkUrl = '';
		$this->editObj = null;
	}
	
	public function updateLink()
	{
		$this->validate();
		$linkData = json_encode(
			[
				'title' => $this->linkText,
				'url' => $this->linkUrl,
			]);
		$this->editObj->value = $linkData;
		$this->editObj->save();
		//notify everyone
		NewClassAlert::dispatch($this->editObj, __('subjects.school.widgets.class-announcements.action.update'));
		$this->clearLinkForm();
	}
	
	public function deleteLink(string $linkId)
	{
		$link = ClassLink::find($linkId);
		if($link)
			$link->delete();
	}
	
	public function render()
	{
		$links = $this->classSession->communicationObjects(ClassLink::class)->get();
		return view('livewire.school.class-management.class-links', compact('links'));
	}
}
