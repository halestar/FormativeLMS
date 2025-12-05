<?php

namespace App\Livewire\Utilities;

use App\Models\People\Person;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class MessageNotifier extends Component
{
	public Person $self;
	public Collection $notifications;
	public string $url;
	
	public function mount()
	{
		$this->self = auth()->user();
		$this->updateUrl();
	}
	
	public function updateUrl()
	{
		$this->notifications = $this->self->classMessageNotifications;
		$latestNotification = $this->notifications?->first() ?? false;
		if($latestNotification)
			$this->url = route('subjects.school.classes.messages', ['notification_id' => $latestNotification->id]);
		else
			$this->url = route('subjects.school.classes.messages');
	}
	
	public function render()
	{
		return view('livewire.utilities.message-notifier');
	}
	
	public function getListeners()
	{
		return
			[
				"echo-private:people.{$this->self->id},.classMessage" => 'receiveMessage',
				'update-message-notifier-status' => 'updateUrl',
				'message-notifications-cleared' => 'updateUrl',
			];
	}
	
	public function receiveMessage()
	{
		$this->updateUrl();
		$this->dispatch('message-notifier-new-message-alert');
	}
}
