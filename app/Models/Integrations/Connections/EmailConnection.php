<?php

namespace App\Models\Integrations\Connections;

use App\Interfaces\IntegrationConnectionInterface;
use App\Mail\SchoolMail;
use App\Models\Integrations\IntegrationConnection;
use App\Models\People\Person;
use App\Notifications\LmsNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

abstract class EmailConnection extends IntegrationConnection implements IntegrationConnectionInterface
{
    final public static function getInstanceDefault(): array
    {
        return [];
    }

    /**
     * This method will send the email to the recipients
     * @param Person|Collection|array $recipients The recipients of the email which can be a single Person, a Collection
     * of Person models, or an array of Person Models.
     * @param SchoolMail $mail The mail that you would like to send.
     * @return void
     */
    abstract public function sendToPerson(Person|string $recipient, SchoolMail $mail): void;
    abstract public function sendToPersonSimple(Person|string $recipient, string $subject, string $body): void;
}