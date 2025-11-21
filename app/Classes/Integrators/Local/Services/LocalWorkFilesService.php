<?php

namespace App\Classes\Integrators\Local\Services;

use App\Classes\Integrators\Local\Connections\LocalWorkFilesConnection;
use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\Integrator;
use App\Models\Integrations\LmsIntegrationService;
use App\Models\People\Person;

class LocalWorkFilesService extends LmsIntegrationService
{
	/**
	 * @inheritDoc
	 */
	public static function getServiceType(): IntegratorServiceTypes
	{
		return IntegratorServiceTypes::WORK;
	}
	
	/**
	 * @inheritDoc
	 */
	public static function getServiceName(): string
	{
		return __('integrators.local.work');
	}
	
	/**
	 * @inheritDoc
	 */
	public static function getServiceDescription(): string
	{
		return __('integrators.local.work.description');
	}
	
	/**
	 * @inheritDoc
	 */
	public static function getDefaultData(): array
	{
		return
			[
				'work_disk' => config('lms.storage.work'),
			];
	}
	
	/**
	 * @inheritDoc
	 */
	public static function canConnectToPeople(): bool
	{
		return false;
	}
	
	/**
	 * @inheritDoc
	 */
	public static function canConnectToSystem(): bool
	{
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	public static function getPath(): string
	{
		return "work";
	}
	
	/**
	 * @inheritDoc
	 */
	public static function canBeConfigured(): bool
	{
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	public function canConnect(Person $person): bool
	{
		return false;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getConnectionClass(): string
	{
		return '';
	}
	
	/**
	 * @inheritDoc
	 */
	public function getSystemConnectionClass(): string
	{
		return LocalWorkFilesConnection::class;
	}
	
	/**
	 * @inheritDoc
	 */
	public function configurationUrl(): string
	{
		return route(Integrator::INTEGRATOR_ACTION_PREFIX . 'local.work.index');
	}
	
	public function systemAutoconnect(): bool
	{
		return true;
	}
	
	public function canSystemConnect(): bool
	{
		return true;
	}
	
	public function canRegister(): bool
	{
		return false;
	}
	
	public function registrationUrl(): string
	{
		return '';
	}
}