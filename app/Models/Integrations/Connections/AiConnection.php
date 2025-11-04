<?php

namespace App\Models\Integrations\Connections;

use App\Interfaces\AiPromptable;
use App\Interfaces\IntegrationConnectionInterface;
use App\Models\Ai\AiPrompt;
use App\Models\Integrations\IntegrationConnection;

abstract class AiConnection extends IntegrationConnection implements IntegrationConnectionInterface
{
	final public static function getSystemInstanceDefault(): array { return []; }
	
	abstract public function getLlms(): array;
	
	abstract public function executePrompt(string $aiModel, AiPrompt $prompt, AiPromptable $target): void;
}