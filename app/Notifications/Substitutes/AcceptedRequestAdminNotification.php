<?php

namespace App\Notifications\Substitutes;

use App\Classes\Settings\ServerSettings;
use App\Models\Substitutes\CampusRequest;
use App\Notifications\SystemNotification;
use Illuminate\Notifications\Messages\MailMessage;

class AcceptedRequestAdminNotification extends SystemNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public CampusRequest $subCampusRequest) {}

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $settings = app()->make(ServerSettings::class);

        return (new MailMessage)
            ->from($settings->get('server.send_email_as'), config('app.name'))
            ->subject('Substitute Accepted Coverage')
            ->view('substitutes.mails.accepted-request-admin',
                [
                    'campusRequest' => $this->subCampusRequest,
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
            'title' => 'Substitute Accepted Coverage',
            'body' => 'A substitute request has been accepted by '.$this->subCampusRequest->substitute->name.' for '.
                $this->subCampusRequest->subRequest->requested_for->format('d/m'),
            'action' => route('substitutes.show', $this->subCampusRequest->subRequest),
        ];
    }

    public function broadcastType(): string
    {
        return 'notification.substitutes.accepted';
    }

    public function toSms(object $notifiable): string
    {
        return 'A substitute request has been accepted by '.$this->subCampusRequest->substitute->name.' for '.
            $this->subCampusRequest->subRequest->requested_for->format('d/m');
    }
}
