<?php

namespace App\Classes\ClassManagement;

use App\Classes\NotificationPayload;
use App\Notifications\ClassAlert;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ClassLinksWidget extends ClassWidget
{
    public string $title;

    /**
     * OVERRIDES
     */
    protected function __construct(string $id, int $order)
    {
        parent::__construct($id, $order);
        $this->title = __('subjects.school.widgets.class-links');
    }

    public function toArray(): array
    {
        $arr = parent::toArray();
        $arr['title'] = $this->title;
        return $arr;
    }

	public function getComponentName(): string
	{
		return "school.class-links";
	}

	public static function hydrate(array $data): ClassWidget
	{
        $widget = new ClassLinksWidget($data['id'], $data['order']);
        $widget->title = $data['title'];
        return $widget;
	}

	public static function getWidgetName(): string
	{
        return __('subjects.school.widgets.class-links');
	}

	public static function create(int $order): ClassWidget
	{
        $id = Str::uuid();
        return new ClassLinksWidget($id, $order);
	}

	public function deleteWidget(): void
	{
		if(!$this->data)
            $this->getData();
        $this->data->delete();
	}

    /**
     * LINKS METHODS
     */
    public function getLinks(): array
    {
        return ($this->getData())['links'] ?? [];
    }

    public function getLink(string $linkId): ?array
    {
        $links = $this->getLinks();
        foreach($links as $l)
        {
            if($l['id'] == $linkId)
                return $l;
        }
        return null;
    }

    public function addLink(array $link): void
    {
        $data = $this->getData();
        $linkData =
            [
                'id' => Str::uuid(),
                'text' => $link['text'],
                'url' => $link['url'],
            ];
        if(!isset($data['links']))
            $data['links'] = [];
        $data['links'][] = $linkData;
        $this->setData($data);
        $session = $this->getClassSession();
        $title = __('subjects.school.widgets.class-links.add.notification.title', ['class' =>  $session->name]);
        $message = __('subjects.school.widgets.class-links.add.notification.body',
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

    public function updateLink(array $link, bool $notify = false): void
    {
        $this->removeLink($link['id']);
        $this->addLink($link);
        if($notify)
        {
            $session = $this->getClassSession();
            $title = __('subjects.school.widgets.class-links.update.notification.title', ['class' =>  $session->name]);
            $message = __('subjects.school.widgets.class-links.update.notification.body',
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

    public function removeLink(string $linkId): ?array
    {
        $links = [];
        $deletedLink = null;
        foreach($this->getLinks() as $l)
        {
            if($l['id'] == $linkId)
            {
                $deletedLink = $l;
                continue;
            }
            $links[] = $l;
        }
        $data = $this->getData();
        $data['links'] = $links;
        $this->setData($data);
        return $deletedLink;
    }
}
