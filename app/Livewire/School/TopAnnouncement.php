<?php

namespace App\Livewire\School;

use App\Classes\ClassManagement\TopAnnouncementWidget;
use App\Models\SubjectMatter\ClassSession;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TopAnnouncement extends Component
{
    public TopAnnouncementWidget $announcement;
    public ?Collection $otherClasses = null;
    public bool $editing = false;
    #[Validate('nullable')]
    public ?string $announcementText = null;
    #[Validate('required|hex_color')]
    public string $announcementColor = '#ffffff';
    #[Validate('required|date_format:Y-m-d')]
    public string $announcementExpiry;
    public array $alsoPost = [];

    public bool $notify = true;
    public function mount(TopAnnouncementWidget $announcement)
    {
        $this->announcement = $announcement;
        if(Auth::user()->isTeacher() && Gate::allows('manage', $this->announcement->owner->owner))
            $this->otherClasses = Auth::user()->currentClassSessions->where('id', '<>', $this->announcement->owner->owner->id);
        else
            $this->otherClasses = new Collection();
    }

    public function edit()
    {
        $this->editing = true;
        $this->announcementText = $this->announcement->getAnnouncement();
        $this->announcementColor = $this->announcement->getAnnouncementColor();
        $this->announcementExpiry = $this->announcement->getAnnouncementExpiry()->format('Y-m-d');
        $this->dispatch('init-editor');
    }

    public function saveAnnouncement()
    {
        $this->validate();
        $this->announcement->setAnnouncement($this->announcementText);
        $this->announcement->setAnnouncementColor($this->announcementColor);
        $this->announcement->setAnnouncementExpiry(Carbon::parse($this->announcementExpiry));
        $this->editing = false;
        $this->announcement->save($this->notify);
        foreach($this->alsoPost as $session_id)
        {
            $session = ClassSession::find($session_id);
            if($session)
            {
                $ta = $session->layout->getTopAnnouncement();
                $ta->setAnnouncement($this->announcementText);
                $ta->setAnnouncementColor($this->announcementColor);
                $ta->setAnnouncementExpiry(Carbon::parse($this->announcementExpiry));
                $ta->save($this->notify);
            }
        }
    }

    protected function messages()
    {
        return [
            'announcementText' => __('errors.top-announcement.text'),
            'announcementColor' => __('errors.top-announcement.color'),
            'announcementExpiry' => __('errors.top-announcement.expires'),
        ];
    }

    public function cancel()
    {
        $this->editing = false;
        $this->dispatch('clear-editor');
    }
    public function render()
    {
        return view('livewire.school.top-announcement');
    }
}
