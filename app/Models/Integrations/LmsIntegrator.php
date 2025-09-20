<?php

namespace App\Models\Integrations;

use App\Classes\Integrators\IntegrationsManager;
use App\Enums\IntegratorServiceTypes;
use App\Models\People\Person;

abstract class LmsIntegrator extends Integrator
{
	public static function autoload(): static
	{
		return static::where('path', static::getPath())
		             ->first();
	}
	
	/**
	 * @return string THis will return the path name that it will prepend anytime a route needs to access this integrator.
	 */
	abstract public static function getPath(): string;
	
	/*****************************************
	 * INSTANCED ABSTRACT FUNCTIONS
	 */
	
	/**
	 * @return string The name of this integrator
	 */
	abstract public static function integratorName(): string;
	
	/**
	 * @return string The description of this integrator
	 */
	abstract public static function integratorDescription(): string;
	
	/**
	 * @return array The default data to save when this integrator is instatiated for the first time.
	 */
	abstract public static function defaultData(): array;
	
	/**
	 * @return string Rreturns the current version of this integrator.
	 */
	abstract public static function getVersion(): string;
	
	/**
	 * @return bool Whether this integrator can connect to people.
	 */
	abstract public static function canConnectToPeople(): bool;
	
	/**
	 * @return bool Whether this integrator can connect to the system.
	 */
	abstract public static function canConnectToSystem(): bool;
	
	/**
	 * @return bool Whether this integrator can be configured.
	 */
	abstract public static function canBeConfigured(): bool;
	
	public function ableToIntegrate(Person $person): bool
	{
		return ($this->enabled && $this->hasAnyRole($person->schoolRoles) && $this->canIntegrate($person));
	}
	
	/**
	 * This function will check if this integrator can integrate with the person (NOT authenticate))
	 * @param Person $person THe person to check
	 * @return bool Whether this integrator can integrate with the person.
	 */
	abstract protected function canIntegrate(Person $person): bool;
	/*****************************************
	 * STATIC FUNCTIONS
	 */
	
	/**
	 * This function rgisters all the service this integrator has.
	 * @param IntegrationsManager $manager The manager to register services with
	 * @param bool $overwrite Whther to overwrite the existing settings.
	 * @return void
	 */
	abstract public function registerServices(IntegrationsManager $manager, bool $overwrite = false): void;
	
	/**
	 * @return bool Whether the integrator is outdated and should be updated.
	 */
	abstract public function isOutdated(): bool;
	
	/**
	 * @param IntegratorServiceTypes $type The type of service to check for
	 * @return bool Whether this integrator has this service.
	 */
	abstract public function hasService(IntegratorServiceTypes $type): bool;
	
	/**
	 * @return string The url to the entry point of the configuration page for this integrator.
	 */
	abstract public function configurationUrl(): string;
	
	/**
	 * @return string The url for the image icon for this integrator.
	 */
	abstract public function getImageUrl(): string;
	
	/**
	 * @param Person $person The person to check the integration status for
	 * @return bool Whether the person is integrated with this integrator.
	 */
	abstract public function isIntegrated(Person $person): bool;
	
	/**
	 * This function will return a URL that will be able to integrate the person with this integrator. The
	 * URL can be a redirect URL, or a URL to a third-party website, or even a URL to an integrator-defined
	 * route.
	 * @param Person $person The person to integrate
	 * @return string The URL that will integrate the person with this integrator.
	 */
	abstract public function integrationUrl(Person $person): string;
	
	/**
	 * This function will remove the integration for the person.
	 * @param Person $person The person to remove the integration for
	 * @return void
	 */
	abstract public function removeIntegration(Person $person): void;
	
	/**
	 * This function will get called in the web routes which will publish all the routes for this integrator.
	 * ALL the routes here will be prefixed by a /integrations/ then integrator's getPath() (@return void
	 * @see LmsIntegrator::getPath())
	 * so if your integrator returns a path of 'local', then the routes will be published as /integrations/local/*
	 */
	abstract public function publishRoutes(): void;
}