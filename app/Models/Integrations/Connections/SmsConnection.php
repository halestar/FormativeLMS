<?php

namespace App\Models\Integrations\Connections;

use App\Interfaces\IntegrationConnectionInterface;
use App\Models\Integrations\IntegrationConnection;

abstract class SmsConnection extends IntegrationConnection implements IntegrationConnectionInterface
{
	
}