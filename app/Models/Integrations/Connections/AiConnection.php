<?php

namespace App\Models\Integrations\Connections;

use App\Interfaces\IntegrationConnectionInterface;
use App\Models\Ai\AiPrompt;
use App\Models\Integrations\IntegrationConnection;

abstract class AiConnection extends IntegrationConnection implements IntegrationConnectionInterface
{
	/**
	 * @return array Will ALWAYS return the instance defaults defined by the subclass, plus the locked settings.
	 */
	final public static function getSystemInstanceDefault(): array { return []; }
	
	abstract public function getLlms(): array;
	/**************************************************************
	 * FINAL FUNCTIONS
	 */
	
	abstract public function executePrompt(string $aiModel, AiPrompt $prompt): void;
}