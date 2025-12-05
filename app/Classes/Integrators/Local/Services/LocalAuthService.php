<?php

namespace App\Classes\Integrators\Local\Services;

use App\Classes\Integrators\Local\Connections\LocalAuthConnection;
use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\Integrator;
use App\Models\Integrations\LmsIntegrationService;
use App\Models\People\Person;
use Illuminate\Support\Facades\Blade;

class LocalAuthService extends LmsIntegrationService
{
	/**
	 * @inheritDoc
	 */
	public static function getServiceType(): IntegratorServiceTypes
	{
		return IntegratorServiceTypes::AUTHENTICATION;
	}
	
	/**
	 * @inheritDoc
	 */
	public static function getServiceName(): string
	{
		return __('integrators.local.auth');
	}
	
	public static function getServiceDescription(): string
	{
		return __('integrators.local.auth.description');
	}
	
	public static function getDefaultData(): array
	{
		return
			[
				'maxAttempts' => 5,
				'decayMinutes' => 1,
				'lockoutTimeout' => 60,
			];
	}
	
	public static function canConnectToPeople(): bool
	{
		return true;
	}
	
	public static function canConnectToSystem(): bool
	{
		return false;
	}
	
	/**
	 * @inheritDoc
	 */
	public static function getPath(): string
	{
		return 'auth';
	}
	
	public static function canBeConfigured(): bool
	{
		return true;
	}
	
	public static function loginButton(): string
	{
		$html = <<< EOHTML
<div class="border border-dark rounded-4 text-bg-secondary fs-5 p-2 fw-bolder">
	<span class="pe-2 me-2"><i class="fa-solid fa-right-to-bracket"></i></span>
	Login Locally
</div>
EOHTML;
		return Blade::render($html);
	}
	
	/**
	 * @inheritDoc
	 */
	public function canConnect(Person $person): bool
	{
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getConnectionClass(): string
	{
		return LocalAuthConnection::class;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getSystemConnectionClass(): string
	{
		return '';
	}
	
	public function configurationUrl(): string
	{
		return route(Integrator::INTEGRATOR_ACTION_PREFIX . 'local.auth.index');
	}
	
	public function systemAutoconnect(): bool
	{
		return false;
	}
	
	public function canSystemConnect(): bool
	{
		return false;
	}
	
	public function canRegister(): bool
	{
		return false;
	}
	
	public function registrationUrl(): string
	{
		return '';
	}

    public function canEnable(): bool
    {
        return true;
    }
}