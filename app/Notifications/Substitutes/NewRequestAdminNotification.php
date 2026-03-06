<?php

namespace App\Notifications\Substitutes;

use App\Classes\Settings\ServerSettings;
use App\Models\Substitutes\SubRequest;
use App\Notifications\SystemNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class NewRequestAdminNotification extends SystemNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public SubRequest $subRequest, public Collection $subs) {}

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $settings = app()->make(ServerSettings::class);

        return (new MailMessage)
            ->from($settings->get('server.send_email_as'), config('app.name'))
            ->subject('New Substitute Request')
            ->view('substitutes.mails.new-request-admin',
                [
                    'subRequest' => $this->subRequest,
                    'subs' => $this->subs,
                ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Substitute Request',
            'body' => 'A new substitute request has been submitted by '.$this->subRequest->submitted_by_name.' for '.
                $this->subRequest->requested_for->format('F d, Y'),
            'action' => route('substitutes.show', $this->subRequest),
        ];
    }

    public function broadcastType(): string
    {
        return 'notification.substitutes.new';
    }

    public function toSms(object $notifiable): string
    {
        return 'A new substitute request has been submitted by '.
            $this->subRequest->submitted_by_name.' for '.
            $this->subRequest->requested_for->format('m/d/y');
    }
}
