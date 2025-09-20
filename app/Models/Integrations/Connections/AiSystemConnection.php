<?php

namespace App\Models\Integrations\Connections;

use App\Interfaces\IntegrationConnectionInterface;
use App\Models\Ai\AiPrompt;
use App\Models\Integrations\IntegrationConnection;

abstract class AiSystemConnection extends IntegrationConnection implements IntegrationConnectionInterface
{
	abstract public function getLlms(): array;
	abstract public function executePrompt(string $aiModel, AiPrompt $prompt): void;
	/**************************************************************
	 * FINAL FUNCTIONS
	 */
	
	/**
	 * @return array Will ALWAYS return the instance defaults defined by the subclass, plus the locked settings.
	 */
	final public static function getInstanceDefault(): array { return []; }
}