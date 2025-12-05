<?php

namespace App\Models\Integrations\Connections;

use App\Interfaces\IntegrationConnectionInterface;
use App\Mail\SchoolMail;
use App\Models\Integrations\IntegrationConnection;
use App\Models\People\Person;
use App\Models\Utilities\SchoolMessage;
use Illuminate\Support\Collection;

abstract class SmsConnection extends IntegrationConnection implements IntegrationConnectionInterface
{
    final public static function getInstanceDefault(): array
    {
        return [];
    }

    abstract public function sendToNumber(string $number, string $message): void;
}