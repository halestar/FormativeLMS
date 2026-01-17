<?php


namespace App\Classes\Integrators;

use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\IntegrationConnection;
use App\Models\Integrations\IntegrationService;
use App\Models\Integrations\Integrator;
use App\Models\People\Person;
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
		return Integrator::enabled()
		                 ->get();
	}
	
	/**
	 * This function returns all the available services for a given type. Available means that the
	 * service is enabled and the integrator is enabled.
	 * @param IntegratorServiceTypes|null $type Optional type of service to return. Else returns all services.
	 * @return Collection The available services.
	 */
	public function getAvailableServices(?IntegratorServiceTypes $type = null): Collection
	{
		$services = IntegrationService::enabled()->whereHas('integrator', fn($q) => $q->enabled());
		if($type)
			$services->where('service_type', $type);
		return $services->get();
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
		$integrator = Integrator::where('className', $integratorClass)
		                        ->first();
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
	public function registerService(Integrator $integrator, string $serviceClass,
		bool $force = false): ?IntegrationService
	{
		/** @var IntegrationService $serviceClass */
		//next, we check if we already have a service defined for this type
		$service = $integrator->getService(($serviceClass)::getServiceType());
		if(!$service)
		{
			//in this case, we need to build the model.
			$service = new ($serviceClass)();
			$service->integrator_id = $integrator->id;
			$service->data = ($service)::getDefaultData();
			$service->service_type = ($service)::getServiceType();
            $service->enabled = $service->canEnable();
		}
		//the service is already registered, so we will update the class name
		$service->className = $serviceClass;
		$service->name = ($service)::getServiceName();
		$service->path = ($service)::getPath();
		$service->description = ($service)::getServiceDescription();
		$service->can_connect_to_people = ($service)::canConnectToPeople();
		$service->can_connect_to_system = ($service)::canConnectToSystem();
		$service->configurable = ($service)::canBeConfigured();
		//we only update the data if the forced flag is set.
		if($force)
		{
			$service->data = ($service)::getDefaultData();
			$service->inherit_permissions = true;
            $service->enabled = $service->canEnable();
		}
		$service->save();
		//is this a system service, should we autoconnect?
		if($service::canConnectToSystem() && $service->systemAutoconnect())
			$service->connectToSystem();
		//If this is a classes service, we also need to invalidate all the classes that are managed by this service.
		if($service::getServiceType() == IntegratorServiceTypes::CLASSES)
		{
			foreach($service->connections as $connection)
				$connection->invalidateClasses();
		}
		return $service;
	}

    public function systemConnections(IntegratorServiceTypes $type = null): Collection
    {
        $query = IntegrationConnection::enabled()->whereNull('person_id');
        if($type)
            $query->whereHas('service', fn($q) => $q->where('service_type', $type));
        return $query->get();
    }
    public function personalConnections(Person $person, IntegratorServiceTypes $type = null): Collection
    {
        $query = IntegrationConnection::enabled()->where('person_id', $person->id);
        if($type)
            $query->whereHas('service', fn($q) => $q->where('service_type', $type));
        return $query->get();
    }

    public function unconnectedPersonalServices(Person $person, IntegratorServiceTypes $type): Collection
    {
        return IntegrationService::enabled()->where('service_type', $type)
            ->whereDoesntHave('personalConnections', fn($q) => $q->where('person_id', $person->id))
            ->get();
    }

    public function hasPersonalConnection(Person $person, IntegratorServiceTypes $type): bool
    {
        return IntegrationConnection::enabled()->where('person_id', $person->id)
            ->whereHas('service', fn($q) => $q->where('service_type', $type))
            ->exists();
    }
}