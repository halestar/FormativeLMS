<?php

namespace App\Notifications\Substitutes;

use App\Classes\Clients\SmsClient;
use App\Models\Substitutes\Substitute;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewSubWelcomeSms extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Substitute $substitute) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [SmsClient::class];
    }

    public function toSms(object $notifiable): string
    {
        return 'You have joined the New Roads Sub Requests texting channel.  You will get notifications about new '.
            'substitute requests from this number. To stop receiving these notifications, reply STOP to this number';
    }
}
