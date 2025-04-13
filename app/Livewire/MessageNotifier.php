<?php

namespace App\Livewire;

use App\Models\People\Person;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class MessageNotifier extends Component
{
    public Person $self;
    public Collection $notifications;
    public bool $glowing = false;
    public string $url;

    public function mount()
    {
        $this->self = auth()->user();
        $this->updateUrl();
    }

    #[On('update-message-notifier-status')]
    public function updateUrl()
    {
        $this->notifications = $this->self->classMessageNotifications;
        $latestNotification = $this->notifications->first();
        if($latestNotification)
            $this->url = route('subjects.school.classes.messages', ['notification_id' => $latestNotification->id]);
        else
            $this->url = route('subjects.school.classes.messages');
    }

    public function render()
    {
        if($this->glowing)
        {
            $shouldGlow = true;
            $this->glowing = false;
        }
        else
            $shouldGlow = false;
        return view('livewire.message-notifier', ['shouldGlow' => $shouldGlow]);
    }

    public function getListeners()
    {
        return
            [
                "echo-private:people.{$this->self->id},.newClassMessage" => 'receiveMessage',
            ];
    }

    public function receiveMessage()
    {
        $this->glowing = true;
        $this->updateUrl();
        $this->dispatch('new-message-alert');
    }
}
