<?php

namespace App\Channels;

use App\Classes\Settings\CommunicationSettings;
use App\Models\People\Person;
use App\Models\People\Phone;
use App\Notifications\LmsNotification;
use Illuminate\Support\Facades\Blade;

class SchoolTexter
{
    /**
     * Authenticate the user's access to the channel.
     */
    public function send(object $notifiable, LmsNotification $notification): void
    {
        $commSettings = app(CommunicationSettings::class);
        //we need make sure that the person is set up to take sms.
        if($notifiable->getPreference('communications.send_sms', false))
        {
            //get the number.
            $phone = Phone::find($notifiable->getPreference('communications.sms_phone_id'));
            if($phone)
            {
                $connection = $commSettings->sms_connection;
                $connection->sendToNumber($phone->phone, Blade::render($notification->message->short_body, $notification->withTokens()));
            }
        }
    }
}
