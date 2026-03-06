<?php

namespace App\Notifications\Substitutes;

use App\Classes\Clients\SmsClient;
use App\Classes\Settings\ServerSettings;
use App\Models\Substitutes\SubRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RejectSubsNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public SubRequest $subRequest) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $via = ['mail'];
        if ($notifiable->sms_confirmed) {
            $via[] = SmsClient::class;
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $settings = app()->make(ServerSettings::class);

        return (new MailMessage)
            ->from($settings->get('server.send_email_as'), config('app.name'))
            ->subject('Coverage has been found!')
            ->view('substitutes.mails.reject-request-sub',
                [
                    'subReq' => $this->subRequest,
                ]);
    }

    public function toSms(object $notifiable): string
    {
        return 'Coverage has been found! Thank you for participating.';
    }
}
