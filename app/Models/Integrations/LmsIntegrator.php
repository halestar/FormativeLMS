<?php

namespace App\Models\Integrations;

use App\Classes\Integrators\IntegrationsManager;
use App\Enums\IntegratorServiceTypes;

abstract class LmsIntegrator extends Integrator
{
    /*****************************************
     * DESCRIPTION FUNCTIONS
     * All these functions are used statically to describe your integrator and establish routes.
     ****************************************/

    /**
     * @return string THis will return the path name that it will prepend anytime a route needs to access this integrator.
     */
    abstract public static function getPath(): string;

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

    /*****************************************
     * INSTANTIATED FUNCTIONS
     */

    /**
     * This function registers all the service this integrator has.
     *
     * @param  IntegrationsManager  $manager  The manager to register services with
     * @param  bool  $overwrite  Whether to overwrite the existing settings.
     */
    abstract public function registerServices(IntegrationsManager $manager, bool $overwrite = false): void;

    /**
     * @return bool Whether the integrator is outdated and should be updated.
     */
    abstract public function isOutdated(): bool;

    /**
     * @param  IntegratorServiceTypes  $type  The type of service to check for
     * @return bool Whether this integrator has this service.
     */
    abstract public function hasService(IntegratorServiceTypes $type): bool;

    /**
     * @return string The url for the image icon for this integrator.
     */
    abstract public function getImageUrl(): string;

    /**
     * This function will get called in the web routes which will publish all the routes for this integrator.
     * ALL the routes here will be prefixed by a /integrations/ then integrator's getPath() (@return void
     *
     * @see LmsIntegrator::getPath())
     * so if your integrator returns a path of 'local', then the routes will be published as /integrations/local/*
     */
    abstract public function publishRoutes(): void;
}
