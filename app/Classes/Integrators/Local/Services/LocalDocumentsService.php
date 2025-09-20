<?php

namespace App\Classes\Integrators\Local\Services;

use App\Classes\Integrators\Local\Connections\LocalDocumentsConnection;
use App\Enums\IntegratorServiceTypes;
use App\Interfaces\Integrators\IntegrationServiceInterface;
use App\Models\Integrations\Integrator;
use App\Models\Integrations\LmsIntegrationService;
use App\Models\People\Person;

class LocalDocumentsService extends LmsIntegrationService
{
	
	public static function getServiceType(): IntegratorServiceTypes
	{
		return IntegratorServiceTypes::DOCUMENTS;
	}
	
	public static function getServiceName(): string
	{
		return __('integrators.local.documents');
	}
	
	public static function getServiceDescription(): string
	{
		return __('integrators.local.documents.description');
	}
	
	public static function getDefaultData(): array
	{
		return
			[
				'documents_disk' => config('lms.storage.documents'),
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
	
	public static function getPath(): string
	{
		return "documents";
	}
	
	public static function canBeConfigured(): bool
	{
		return true;
	}
	
	public function canConnect(Person $person): bool
	{
		return true;
	}
	
	public function getConnectionClass(): string
	{
		return LocalDocumentsConnection::class;
	}
	
	public function getSystemConnectionClass(): string
	{
		return '';
	}
	
	public function configurationUrl(): string
	{
		return route(Integrator::INTEGRATOR_ACTION_PREFIX . 'local.documents.index');
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
}