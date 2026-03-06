<?php

namespace App\Notifications\Substitutes;

use App\Classes\Settings\ServerSettings;
use App\Models\Substitutes\Substitute;
use App\Notifications\SystemNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSubSignupNotification extends SystemNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public Substitute $substitute) {}

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $settings = app()->make(ServerSettings::class);

        return (new MailMessage)
            ->from($settings->get('server.send_email_as'), config('app.name'))
            ->subject('New sub has been accepted!')
            ->line('A new substitute, '.$this->substitute->name.' has been accepted as a Substitute for '.
                $this->substitute->campuses->pluck('abbr')->join(', '))
            ->action('View the substitute', route('substitutes.pool.show', $this->substitute));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return
            [
                'title' => 'New Substitute Notification',
                'body' => 'A new substitute, '.$this->substitute->name.' has been accepted as a Substitute for '.
                    $this->substitute->campuses->pluck('abbr')->join(', '),
                'action' => route('substitutes.pool.show', $this->substitute),
            ];
    }

    public function broadcastType(): string
    {
        return 'notification.substitute.new';
    }

    public function toSms(object $notifiable): string
    {
        return 'A new substitute, '.$this->substitute->name.' has been accepted as a Substitute for '.
            $this->substitute->campuses->pluck('abbr')->join(', ');
    }
}
