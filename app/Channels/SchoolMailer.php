<?php

namespace App\Channels;

use App\Classes\Settings\CommunicationSettings;
use App\Notifications\LmsNotification;

class SchoolMailer
{
    /**
     * Authenticate the user's access to the channel.
     */
    public function send(object $notifiable, LmsNotification $notification): void
    {
        $commSettings = app(CommunicationSettings::class);
        $connection = $commSettings->email_connection;
        $connection->sendToPerson($notifiable, $notification->toMail());
    }
}
