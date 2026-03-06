<?php

namespace App\Notifications\Substitutes;

use App\Classes\Clients\SmsClient;
use App\Classes\Settings\ServerSettings;
use App\Models\Substitutes\SubRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRequestSubNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public SubRequest $subRequest, public string $plainTextToken) {}

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
            ->subject('New Substitute Request')
            ->view('substitutes.mails.new-request-sub',
                [
                    'subReq' => $this->subRequest,
                    'link' => route('subs.request', ['token' => $this->plainTextToken]),
                ]);
    }

    public function toSms(object $notifiable): string
    {
        return 'Coverage is needed for '.$this->subRequest->requester_name.' on '.
            $this->subRequest->requested_for->format('m/d').' from '.
            $this->subRequest->startTime()->format('g:i A').' to '.
            $this->subRequest->endTime()->format('g:i A').'. Please go to '.
            route('subs.request', ['token' => $this->plainTextToken]).
            ' to accept.';
    }
}
