<?php

namespace App\Classes\ClassManagement;

use App\Classes\NotificationPayload;
use App\Interfaces\HasTopAnnouncement;
use App\Notifications\ClassAlert;
use App\Traits\DeterminesTextColor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class TopAnnouncementWidget
{

    use DeterminesTextColor;
    public ?string $announcement;
    public string $color;
    public ?Carbon $expiry;
    public ClassSessionLayoutManager $owner;
    public function __construct(array $data, ClassSessionLayoutManager $owner)
    {
        $this->owner = $owner;
        $this->announcement = $data['announcement'];
        $this->color = $data['color'];
        $this->expiry = Carbon::parse($data['expiry']);
    }

    public function getAnnouncement(): ?string
	{
		return $this->announcement;
	}

	public function setAnnouncement(?string $announcement): void
	{
		$this->announcement = $announcement;
	}

	public function hasAnnouncement(): bool
	{
		return ($this->announcement && $this->announcement !== '' && !$this->expiry->isPast());
	}

	public function getAnnouncementColor(): string
	{
		return $this->color;
	}

	public function setAnnouncementColor(string $color): void
	{
		$this->color = $color;
	}

	public function getAnnouncementExpiry(): Carbon
	{
		return $this->expiry;
	}

	public function setAnnouncementExpiry(Carbon $expiry): void
	{
		$this->expiry = $expiry;
	}

	public function canManageAnnouncement(): bool
	{
		return $this->owner->canManage();
	}

    public function save(bool $notify = true)
    {
        $this->owner->setTopAnnouncement($this);
        if($notify)
        {
            $session = $this->owner->owner;
            $title = __('subjects.school.widgets.top-announcement.notification.title', ['class' =>  $session->name]);
            $message = __('subjects.school.widgets.top-announcement.notification.body',
                [
                    'class' => $session->name,
                    'postedOn' => date('m/d'),
                ]);
            $notification = new NotificationPayload($title, $message);
            $recipients = [];
            foreach($session->students as $student)
                $recipients[] = $student->person;
            Notification::send($recipients, new ClassAlert($session, $notification));
        }
    }
}
