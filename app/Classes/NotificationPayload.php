<?php

namespace App\Classes;

class NotificationPayload
{
    public string $title;
    public string $message;
    public ?string $icon = null;
    public string $bgColor = "#ffffff";
    public string $textColor = "#000000";
    public string $borderColor = "#000000";
    public ?string $url = null;
    public ?array $misc = null;

    public function __construct(string $title, string $message)
    {
        $this->title = $title;
        $this->message = $message;
    }

    public function toArray(): array
    {
        return
        [
            'title' => $this->title,
            'message' => $this->message,
            'icon' => $this->icon,
            'bgColor' => $this->bgColor,
            'textColor' => $this->textColor,
            'borderColor' => $this->borderColor,
            'url' => $this->url,
            'misc' => $this->misc,
        ];
    }

    public static function hydrate(array $data): NotificationPayload
    {
        $notification = new NotificationPayload($data['title'], $data['message']);
        $notification->icon = $data['icon'];
        $notification->bgColor = $data['bgColor'];
        $notification->textColor = $data['textColor'];
        $notification->borderColor = $data['borderColor'];
        $notification->url = $data['url'];
        $notification->misc = $data['misc'];
        return $notification;
    }
}
