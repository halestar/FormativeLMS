<?php

namespace App\Classes\Integrators\Local\Services;

use App\Classes\Integrators\Local\Connections\LocalAiConnection;
use App\Classes\Integrators\Local\LocalIntegrator;
use App\Classes\Settings\AiSettings;
use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\Integrator;
use App\Models\Integrations\LmsIntegrationService;
use App\Models\People\Person;
use Illuminate\Support\Facades\Http;

class LocalAiService extends LmsIntegrationService
{
	/**
	 * {@inheritDoc}
	 */
	public static function getServiceType(): IntegratorServiceTypes
	{
		return IntegratorServiceTypes::AI;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getServiceName(): string
	{
		return __('integrators.local.ai');
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getServiceDescription(): string
	{
		return __('integrators.local.ai.description');
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getDefaultData(): array
	{
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function canConnectToPeople(): bool
	{
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function canConnectToSystem(): bool
	{
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getPath(): string
	{
		return 'ai';
	}

	public function canEnable(): bool
	{
		return true;
	}

	public function getConnectionClass(): string
	{
		return LocalAiConnection::class;
	}

	public function canConnect(?Person $person = null): bool
	{
		// To connect, the user/system must register first, so they can only connect if they have a connection
		return $this->hasConnection($person);
	}

	public function canRegister(?Person $person = null): bool
	{
		//system can ALWAYS register
		if (!$person) return true;
		//person can register if the settings allow it.
		$settings = app()->make(AiSettings::class);
		return $settings->allow_user_ai;
	}

	public function canConfigure(?Person $person = null): bool
	{
		//system can ALWAYS configure
		if (!$person) return true;
		//person can configure if the settings allow it.
		$settings = app()->make(AiSettings::class);
		return $settings->allow_user_ai;
	}

	public function registrationUrl(?Person $person = null): ?string
	{
		if (!$person) return route(
			Integrator::INTEGRATOR_ACTION_PREFIX . LocalIntegrator::getPath() . '.services.ai.config'
		);
		$settings = app()->make(AiSettings::class);
		if (!$settings->allow_user_ai) return null;
		return route(
			Integrator::INTEGRATOR_ACTION_PREFIX . LocalIntegrator::getPath() . '.services.ai.config.personal'
		);
	}

	public function configurationUrl(?Person $person = null): ?string
	{
		if (!$person) return route(
			Integrator::INTEGRATOR_ACTION_PREFIX . LocalIntegrator::getPath() . '.services.ai.config'
		);
		$settings = app()->make(AiSettings::class);
		if (!$settings->allow_user_ai) return null;
		return route(
			Integrator::INTEGRATOR_ACTION_PREFIX . LocalIntegrator::getPath() . '.services.ai.config.personal'
		);
	}

	public function testConnection($endpoint): bool
	{
		try
		{
			$response = Http::get($endpoint);
		}
		catch (\Exception $e)
		{
			return false;
		}

		return $response->body() == 'Ollama is running';
	}
}
