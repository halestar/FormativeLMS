<?php

namespace App\Models\Integrations;

use App\Enums\IntegratorServiceTypes;
use App\Models\People\Person;

abstract class LmsIntegrationService extends IntegrationService
{
    /*****************************************
     * BASIC SERVICE INFO
     * This section describes what the service is and what it contains. Most of these
     * methods are used when registering the service to the system.
     *****************************************/

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
     * @return bool Whether this service can connect to a user in the system
     */
    abstract public static function canConnectToPeople(): bool;

    /**
     * @return bool Whether this service can connect to the system
     */
    abstract public static function canConnectToSystem(): bool;

    /**
     * @return string This will return the path name that it will prepend anytime a route needs to access this service
     */
    abstract public static function getPath(): string;

    /**
     * This should return true if the service can be auto-enabled or if the correct conditions are set for the service
     * to be enabled.
     *
     * @return bool Whether this service can be enabled at the current time.
     */
    abstract public function canEnable(): bool;

    /**
     * @return string The class name to use for the connection.
     */
    abstract public function getConnectionClass(): string;

    /*****************************************
     * ACCESS TO SERVICE
     * This defines the 3 ways that the user or system can interact with this service:
     * - Connect: whether the user or system can currently connect to this service.
     * - Register: whether the user or system can register with the service in order to connect.
     * - Configure: whether the connection between the user or system can be configured.
     *****************************************/

    /**
     * @param  Person|null  $person  The person to establish the connection with or null if it's the system.
     * @return bool Whether a connection between this service and the person/system can be established. If false, it means
     *              The user must attempt to register the connection first to be able to connect.
     */
    abstract public function canConnect(?Person $person = null): bool;

    /**
     * @param  Person|null  $person  The person to register the connection with or null if it's the system.
     * @return bool Whether the person or system can register to this service.
     */
    abstract public function canRegister(?Person $person = null): bool;

    /**
     * @param  Person|null  $person  The person to configure the connection with or null if it's the system.
     * @return bool Whether this integration service can be configured by the user or service.
     */
    abstract public function canConfigure(?Person $person = null): bool;

    /*****************************************
     * URLs
     * This section describes the URL's to the registration and configuration pages. Connections are all done
     * through code, so there are no pages for that.
     *****************************************/

    /**
     * This is the URL that the users/system will be redirected to if they need to register to the service. This can be
     * a redirect link to authenticate, or a form to fill in.
     *
     * @param  Person|null  $person  The person trying to register to the service. Null if it's the system attempting this.
     * @return string|null The route to the registration page, null if there isn't one.
     */
    abstract public function registrationUrl(?Person $person = null): ?string;

    /**
     * This is the URL that the users/system will be redirected to when they need to configure this service. Note that
     * this is only possible when the connection exists.
     *
     * @param  Person|null  $person  The person trying to configure to the service. Null if it's the system attempting this.
     * @return string|null The url to the entry point of the configuration page for this integration service.
     */
    abstract public function configurationUrl(?Person $person = null): ?string;
}
