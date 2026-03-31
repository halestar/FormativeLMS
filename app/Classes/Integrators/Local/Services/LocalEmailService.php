<?php

namespace App\Classes\Integrators\Local\Services;

use App\Classes\Integrators\Local\Connections\LocalEmailConnection;
use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\LmsIntegrationService;
use App\Models\People\Person;

class LocalEmailService extends LmsIntegrationService
{
    /**
     * {@inheritDoc}
     */
    public static function getServiceType(): IntegratorServiceTypes
    {
        return IntegratorServiceTypes::EMAIL;
    }

    /**
     * {@inheritDoc}
     */
    public static function getServiceName(): string
    {
        return __('integrators.local.email');
    }

    /**
     * {@inheritDoc}
     */
    public static function getServiceDescription(): string
    {
        return __('integrators.local.email.description');
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
        return false;
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
        return 'email';
    }

    public function canEnable(): bool
    {
        return true;
    }

    public function getConnectionClass(): string
    {
        return LocalEmailConnection::class;
    }

    public function canConnect(?Person $person = null): bool
    {
        return $person == null;
    }

    public function canRegister(?Person $person = null): bool
    {
        return false;
    }

    public function canConfigure(?Person $person = null): bool
    {
        return false;
    }

    public function registrationUrl(?Person $person = null): ?string
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function configurationUrl(?Person $person = null): ?string
    {
        return null;
    }
}
