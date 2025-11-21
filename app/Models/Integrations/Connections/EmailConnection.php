<?php

namespace App\Models\Integrations\Connections;

use App\Interfaces\IntegrationConnectionInterface;
use App\Models\Integrations\IntegrationConnection;

abstract class EmailConnection extends IntegrationConnection implements IntegrationConnectionInterface {}