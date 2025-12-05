<?php

namespace App\Classes\Integrators\Local\Connections;

use App\Classes\Settings\CommunicationSettings;
use App\Mail\SchoolMail;
use App\Models\Integrations\Connections\EmailConnection;
use App\Models\People\Person;
use App\Notifications\LmsNotification;
use Illuminate\Mail\Message;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;

class LocalEmailConnection extends EmailConnection
{

    /**
     * @inheritDoc
     */
    public static function getSystemInstanceDefault(): array
    {
        return [];
    }

    public function sendToPerson(Person|string $recipient, SchoolMail $mail): void
    {
        Mail::to($recipient instanceof Person ? $recipient->system_email : $recipient)->send($mail);
    }

    public function sendToPersonSimple(Person|string $recipient, string $subject, string $body): void
    {
        Mail::raw($body, function (Message $message) use ($recipient, $subject)
        {
            $settings = app(CommunicationSettings::class);
            $message->subject($subject)
                ->to($recipient instanceof Person ? $recipient->system_email : $recipient)
                ->from($settings->email_from_address, $settings->email_from_name);
        });
    }
}