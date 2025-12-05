<?php

namespace App\Classes\Integrators\Local\Services;

use App\Classes\Integrators\Local\Connections\LocalEmailConnection;
use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\Integrator;
use App\Models\Integrations\LmsIntegrationService;
use App\Models\People\Person;

class LocalEmailService extends LmsIntegrationService
{

    /**
     * @inheritDoc
     */
    public static function getServiceType(): IntegratorServiceTypes
    {
        return IntegratorServiceTypes::EMAIL;
    }

    /**
     * @inheritDoc
     */
    public static function getServiceName(): string
    {
        return __('integrators.local.email');
    }

    /**
     * @inheritDoc
     */
    public static function getServiceDescription(): string
    {
        return __('integrators.local.email.description');
    }

    /**
     * @inheritDoc
     */
    public static function getDefaultData(): array
    {
        return [];
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
        return "email";
    }

    /**
     * @inheritDoc
     */
    public static function canBeConfigured(): bool
    {
        return false;
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
    public function canSystemConnect(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getSystemConnectionClass(): string
    {
        return LocalEmailConnection::class;
    }

    /**
     * @inheritDoc
     */
    public function canRegister(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function registrationUrl(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function systemAutoconnect(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function configurationUrl(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function canEnable(): bool
    {
        return true;
    }
}