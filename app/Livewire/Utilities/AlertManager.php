<?php

namespace App\Livewire\Utilities;

use App\Models\People\Person;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class AlertManager extends Component
{
    public Person $self;
    public Collection $notifications;
    public int $unreadCount = 0;
    public bool $showing = false;

    public function mount()
    {
        $this->self = auth()->user();
        $this->notifications = $this->self->lmsNotifications;
    }

    public function markAllAsRead()
    {
        $this->self->lmsNotifications()->unread()->get()->markAsRead();
        $this->unreadCount = 0;
    }

    public function removeNotification($notificationId)
    {
        $this->self->lmsNotifications()->where('id', $notificationId)->delete();
        $this->notifications = $this->self->lmsNotifications;
        $this->showing = ($this->notifications->count() == 0);
    }

    public function removeAllNotifications()
    {
        $this->self->lmsNotifications()->delete();
        $this->notifications = collect();
        $this->unreadCount = 0;
        $this->showing = false;
    }

    public function getListeners()
    {
        return
            [
                "echo-private:people.{$this->self->id},.lmsNotification" => 'receiveMessage',
	            'alert-manager-read-notifications' => 'updateMessages',
            ];
    }

	public function updateMessages()
	{
		$this->notifications = $this->self->lmsNotifications;
		$this->unreadCount = $this->self->lmsNotifications()->unread()->count();
	}

    public function receiveMessage($notification)
    {
		$this->updateMessages();
    }

    public function render()
    {
        $unreadCount = $this->self->lmsNotifications()->unread()->count();
        if($unreadCount != $this->unreadCount)
            $this->self->lmsNotifications()->unread()->get()->markAsRead();
        $this->unreadCount = $unreadCount;
        return view('livewire.utilities.alert-manager');
    }
}
