<?php


namespace App\Classes\Integrators;

use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\IntegrationService;
use App\Models\Integrations\Integrator;
use Illuminate\Support\Collection;

class IntegrationsManager
{
	public function __construct() {}
	
	/**
	 * This function returns all the integrators registered in the system.
	 * @return Collection The integrators.
	 */
	public function allIntegrators(): Collection
	{
		return Integrator::all();
	}
	
	/**
	 * This function returns all the available integrators. Available means that the
	 * integrator is enabled.
	 * @return Collection The available integrators.
	 */
	public function availableIntegrators(): Collection
	{
		return Integrator::enabled()->get();
	}
	
	/**
	 * This function returns all the available services for a given type. Available means that the
	 * service is enabled and the integrator is enabled.
	 * @param IntegratorServiceTypes|null $type Optional type of service to return. Else returns all services.
	 * @return Collection The available services.
	 */
	public function getAvailableServices(?IntegratorServiceTypes $type = null): Collection
	{
		$services = IntegrationService::select('integration_services.*')
			->join('integrators', 'integrators.id', '=', 'integration_services.integrator_id');
		if($type)
			$services->where('integration_services.service_type', $type);
		return $services->where('integrators.enabled', true)
			->where('integration_services.enabled', true)
			->get();
	}
	
	/**
	 * This function will register a new integrator into the system.
	 * @param string $integratorClass The class name of the integrator to register.
	 * @param bool $force Whether we should ovewrite the integrator with the default settings.
	 * @return Integrator|null The integrator code if the registration was successful. Else null.
	 */
	public function registerIntegrator(string $integratorClass, bool $force = false): ?Integrator
	{
		//search for an existing model
		/* @var Integrator $integratorClass */
		$integrator = Integrator::where('className', $integratorClass)->first();
		if(!$integrator)
		{
			//in this case, we need to create it.
			$integrator = new ($integratorClass)();
			$integrator->className = $integratorClass;
			$integrator->data = ($integrator)::defaultData();
		}
		//The following information should always be overwritten.
		$integrator->name = ($integrator)::integratorName();
		$integrator->path = ($integrator)::getPath();
		$integrator->description = ($integrator)::integratorDescription();
		$integrator->has_personal_connections = ($integrator)::canConnectToPeople();
		$integrator->has_system_connections = ($integrator)::canConnectToSystem();
		$integrator->configurable = ($integrator)::canBeConfigured();
		$integrator->enabled = true;
		$integrator->version = ($integrator)::getVersion();
		if($force)
			$integrator->data = ($integrator)::defaultData();
		$integrator->save();
		$integrator->refresh();
		//and we register the services.
		$integrator->registerServices($this, $force);
		return $integrator;
	}
	
	/**
	 * This function will register a service for a given integrator.
	 * @param Integrator $integrator The integrator to register the service for.
	 * @param string $serviceClass The name of the service class to register.
	 * @param bool $force Whether the service should be reverted to the original, default settings.
	 * @return IntegrationService|null
	 */
	public function registerService(Integrator $integrator, string $serviceClass, bool $force = false): ?IntegrationService
	{
		/** @var IntegrationService $serviceClass */
		//next, we check if we already have a service defined for this type
		$service = $integrator->services()->ofType(($serviceClass)::getServiceType())->first();
		if(!$service)
		{
			//in this case, we need to build the model.
			$service = new ($serviceClass)();
			$service->integrator_id = $integrator->id;
			$service->data = ($service)::getDefaultData();
			$service->service_type = ($service)::getServiceType();
		}
		//the service is already registered, so we will update the class name
		$service->className = $serviceClass;
		$service->name = ($service)::getServiceName();
		$service->path = ($service)::getPath();
		$service->description = ($service)::getServiceDescription();
		$service->can_connect_to_people = ($service)::canConnectToPeople();
		$service->can_connect_to_system = ($service)::canConnectToSystem();
		$service->configurable = ($service)::canBeConfigured();
		$service->enabled = true;
		//we only update the data if the forced flag is set.
		if($force)
		{
			$service->data = ($service)::getDefaultData();
			$service->inherit_permissions = true;
		}
		$service->save();
		//is this a system service, should we autoconnect?
		if($service::canConnectToSystem() && $service->systemAutoconnect())
			$service->connectToSystem();
		return $service;
	}
}