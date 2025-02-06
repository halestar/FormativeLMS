<?php

namespace App\Classes\ClassManagement;

use App\Classes\NotificationPayload;
use App\Notifications\ClassAlert;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ClassAnnouncementsWidget extends ClassWidget
{
    protected string $title;

    /**
     * OVERRIDES
     */
    protected function __construct(string $id, int $order)
    {
        parent::__construct($id, $order);
        $this->title = __('school.widgets.class-announcements');
    }

    public function toArray(): array
    {
        $arr = parent::toArray();
        $arr['title'] = $this->title;
        return $arr;
    }

    /**
     * ABSTRACT METHODS
     */

    public function getComponentName(): string
    {
        return "school.class-announcements";
    }

	public static function hydrate(array $data): ClassWidget
	{
		$widget = new ClassAnnouncementsWidget($data['id'], $data['order']);
        $widget->title = $data['title'];
        return $widget;
	}

    public function getTitle(): string
    {
        return $this->title;
    }

    public static function getWidgetName(): string
    {
        return __('subjects.school.widgets.class-announcements');
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public static function create(int $order): ClassWidget
    {
        $id = Str::uuid();
        return new ClassAnnouncementsWidget($id, $order);
    }

    public function deleteWidget(): void
    {
        if(!$this->data)
            $this->getData();
        $this->data->delete();
    }

    /**
     * ANNOUNCEMENT METHODS
     */
    public function getAnnouncements(): array
    {
        $announcement = collect(($this->getData())['announcements'] ?? []);
        return $announcement->filter(function(array $a)
        {
            $postFrom = Carbon::parse($a['post_from']);
            $postTo = Carbon::parse($a['post_to']);
            return $postFrom->isPast() && $postTo->isFuture();
        })
            ->sortByDesc('post_from')
            ->all();
    }

    public function getAllAnnouncements(): array
    {
        return ($this->getData())['announcements'] ?? [];
    }

    public function getAnnouncement(string $announcementId): ?array
    {
        $announcements = $this->getAllAnnouncements();
        foreach($announcements as $a)
        {
            if($a['id'] == $announcementId)
                return $a;
        }
        return null;
    }

    public function addAnnouncement(array $announcement): void
    {
        $data = $this->getData();
        $announcementData =
            [
                'id' => Str::uuid(),
                'title' => $announcement['title'],
                'announcement' => $announcement['announcement'],
                'color' => $announcement['color'],
                'post_from' => $announcement['post_from'],
                'post_to' => $announcement['post_to'],
            ];
        if(!isset($data['announcements']))
            $data['announcements'] = [];
        $data['announcements'][] = $announcementData;
        $this->setData($data);
        $session = $this->getClassSession();
        $title = __('subjects.school.widgets.class-announcements.add.notification.title', ['class' =>  $session->name]);
        $message = __('subjects.school.widgets.class-announcements.add.notification.body',
            [
                'class' => $session->name,
                'postedOn' => Carbon::parse($announcement['post_from'])->format('m/d'),
            ]);
        $notification = new NotificationPayload($title, $message);
        $recipients = [];
        foreach($session->students as $student)
            $recipients[] = $student->person;
        Notification::send($recipients, new ClassAlert($session, $notification));
    }

    public function updateAnnouncement(array $announcement, bool $notify = false): void
    {
        $this->removeAnnouncement($announcement['id']);
        $this->addAnnouncement($announcement);
        if($notify)
        {
            $session = $this->getClassSession();
            $title = __('subjects.school.widgets.class-announcements.update.notification.title', ['class' =>  $session->name]);
            $message = __('subjects.school.widgets.class-announcements.update.notification.body',
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

    public function removeAnnouncement(string $announcementId): ?array
    {
        $announcements = [];
        $deletedAnnouncement = null;
        foreach($this->getAllAnnouncements() as $a)
        {
            if($a['id'] == $announcementId)
            {
                $deletedAnnouncement = $a;
                continue;
            }
            $announcements[] = $a;
        }
        $data = $this->getData();
        $data['announcements'] = $announcements;
        $this->setData($data);
        return $deletedAnnouncement;
    }


}
