<?php

namespace App\Models\Integrations;

use App\Enums\IntegratorServiceTypes;
use App\Models\People\Person;

abstract class LmsIntegrationService extends IntegrationService 
{
	/*****************************************
	 * CONNECTIONS TO USERS
	 */
	
	/**
	 * @param Person $person The person to check if we have a connection to.
	 * @return bool Whether the person has a connection to this service.
	 */
	public function hasServiceConnection(Person $person): bool
	{
		return $this->personalConnections()->where('person_id', $person->id)->exists();
	}
	
	public function registerServiceConnection(Person $person, $data = null): void
	{
		$this->personalConnections()->attach($person->id, $data);
	}
	
	public function forgetServiceConnection(Person $person): void
	{
		$this->personalConnections()->detach($person->id);
	}
	
	public function getServiceConnection(Person $person, ?array $registerData = null): ?IntegrationConnection
	{
		if(is_array($registerData) && !$this->hasServiceConnection($person))
			$this->registerServiceConnection($person, $registerData);
		return IntegrationConnection::where('service_id', $this->id)->where('person_id', $person->id)->first();
	}
	
	/**
	 * @return bool Whether there is a connection already established.
	 */
	final public function isConnected(): bool
	{
		return ($this->activeConnection !== null);
	}
	
	/**
	 * @param Person $person The person to check if we're connected to.
	 * @return bool Whether we're connected to the person.
	 */
	final public function isConnectedTo(Person $person): bool
	{
		return ($this->isConnected() && $this->activeConnection->person_id == $person->id);
	}
	
	/**
	 * This function closes the existing connection
	 * @return void
	 */
	final public function closeConnection(): void
	{
		$this->activeConnection = null;
	}
	
	/**
	 * Attempts to connect this service to the person.
	 * @param Person $person The person to connect to
	 * @return IntegrationConnection|null Returns the connection if it was successfully connected, else null.
	 */
	final public function connect(Person $person): ?IntegrationConnection
	{
		//check if we can connect to this person
		if(!$this->ableToConnect($person)) return null;
		//check if the connection already exists
		if($this->isConnectedTo($person)) return $this->activeConnection;
		//if we're connected to someone else, close the connection
		if($this->isConnected()) $this->closeConnection();
		//since we can connect, check if the connection is already established, else establish it.
		if(!$this->hasServiceConnection($person))
		{
			$data =
				[
					'data' => ($this->getConnectionClass())::getInstanceDefault(),
					'className' => $this->getConnectionClass(),
					'enabled' => true,
				];
			$this->registerServiceConnection($person, $data);
		}
		//attempt to get the connection
		$connection = $this->getServiceConnection($person);
		//but is it enabled?
		if($connection->enabled)
			$this->activeConnection = $connection;
		return $this->activeConnection;
	}
	
	/**
	 * This function erasaes the connection between this service and the person.  It also erases saved settings.
	 * @param Person|null $person The person to disconnect from. If null, we're disconnecting from the person we're currently connected to
	 * @return void
	 */
	public function forgetConnection(): void
	{
		if($this->activeConnection)
			$this->forgetServiceConnection($this->activeConnection->person);
	}
	
	final public function ableToConnect(Person $person)
	{
		
		return $this->enabled && $this->integrator->enabled && $person->hasAnyRole($this->schoolRoles)  && $this->canConnect($person);
	}
	
	/*****************************************
	 * CONNECTIONS TO SYSTEM
	 */
	
	public function hasSystemConnection(): bool
	{
		return IntegrationConnection::where('service_id', $this->id)->where('person_id', null)->exists();
	}
	
	public function registerSystemServiceConnection($data = null): void
	{
		$data['person_id'] = null;
		$data['service_id'] = $this->id;
		IntegrationConnection::create($data);
	}
	
	public function forgetSystemServiceConnection(): void
	{
		IntegrationConnection::where('service_id', $this->id)
		                     ->where('person_id', null)
		                     ->delete();
	}
	
	public function getSystemServiceConnection(): ?IntegrationConnection
	{
		return IntegrationConnection::where('service_id', $this->id)->where('person_id', null)->first();
	}
	
	/**
	 * @return bool Whether we're connected to the FABLMS system.
	 */
	public function isConnectedToSystem(): bool
	{
		return ($this->isConnected() && $this->activeConnection->person_id == null);
	}
	
	/**
	 * This function attempts to connect this service to the FABLMS system.
	 * @return LmsIntegrationConnection|null Returns the established connection, null otherwise.
	 */
	public function connectToSystem(): ?IntegrationConnection
	{
		//check if we can connect to system.
		if(!$this->canSystemConnect()) return null;
		//check if the connection already exists
		if($this->isConnectedToSystem()) return $this->activeConnection;
		//if we're connected to someone else, close the connection
		if($this->isConnected()) $this->closeConnection();
		//since we can connect, check if the connection is already established.
		if(!$this->hasSystemConnection())
		{
			$data =
				[
					'data' => ($this->getSystemConnectionClass())::getSystemInstanceDefault(),
					'className' => $this->getSystemConnectionClass(),
					'enabled' => true,
				];
			$this->registerSystemServiceConnection($data);
		}
		//if we're connected to the system, save the connection and return true.
		$this->activeConnection = $this->getSystemServiceConnection();
		return $this->activeConnection;
	}
	
	public function forgetSystemConnection(): void
	{
		if(!$this->isConnectedToSystem()) return;
		$this->activeConnection->delete();
	}
	
	/*****************************************
	 * INSTANCED ABSTRACT FUNCTIONS
	 */
	
	/**
	 * @param Person $person The person to establish the connection with
	 * @return bool Whether a connection between this service and the person can be established. If false, it means
	 * The user must attempt to register the connection first, thus establishing the connection.
	 */
	abstract public function canConnect(Person $person): bool;
	
	/**
	 * This function is slightly different from the connection functions. In some cases, people might be able
	 * to connect to a service, but not until they enter some data, such as a key or an authentication code.
	 * In those cases, while it is might not be possible to connect, it might be possible to register to this
	 * service in order to be able to connect. This function will return true if users (not the system, as that is
	 * handled through the autoconnect function) can register to this service.
	 * @return bool Whether users may register to the connection.
	 */
	abstract public function canRegister(): bool;
	
	/**
	 * If users ARE going to be able to register to this service, this function will return the route with a form
	 * that will allow them to register them for the service and connect them.
	 * @return string The route to the registration page.
	 */
	abstract public function registrationUrl(): string;
	
	/**
	 * Similar to the above function, but for the system connection
	 * @return bool Whether a connection between this service and the system can be established.
	 */
	abstract public function canSystemConnect(): bool;
	
	
	/**
	 * @return string The class name to use for the connection.
	 */
	abstract public function getConnectionClass(): string;
	
	/**
	 * @return string The class name to use for the connection to the system
	 */
	abstract public function getSystemConnectionClass(): string;
	
	/**
	 * @return bool Whether this service should be autoconnected to the system on registration
	 */
	abstract public function systemAutoconnect(): bool;
	
	/**
	 * @return string The url to the entry point of the configuration page for this integration service.
	 */
	abstract public function configurationUrl(): string;
	
	/*****************************************
	 * STATIC ABSTRACT FUNCTIONS
	 */
	
	/**
	 * @return IntegratorServiceTypes The type of service this is.
	 */
	abstract public static function getServiceType(): IntegratorServiceTypes;
	
	/**
	 * @return string The name of this service.
	 */
	abstract public static function getServiceName(): string;
	
	/**
	 * @return string The description of this service.
	 */
	abstract public static function getServiceDescription(): string;
	
	/**
	 * @return array The default data to save for this service.
	 */
	abstract public static function getDefaultData(): array;
	
	/**
	 * @return bool Whether this service can connect to a user in the systsem
	 */
	abstract public static function canConnectToPeople(): bool;
	
	/**
	 * @return bool Whether this service can connect to the system
	 */
	abstract public static function canConnectToSystem(): bool;
	
	/**
	 * @return string THis will return the path name that it will prepend anytime a route needs to access this service
	 */
	abstract public static function getPath(): string;
	
	/**
	 * @return bool Whether this integration service can be configured.
	 */
	abstract public static function canBeConfigured(): bool;
}