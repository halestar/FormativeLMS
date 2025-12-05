<?php

namespace App\Classes\Integrators\Local\Services;

use App\Classes\Integrators\Local\Connections\LocalSmsConnection;
use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\LmsIntegrationService;
use App\Models\People\Person;

class LocalSmsService extends LmsIntegrationService
{

    /**
     * @inheritDoc
     */
    public static function getServiceType(): IntegratorServiceTypes
    {
        return IntegratorServiceTypes::SMS;
    }

    /**
     * @inheritDoc
     */
    public static function getServiceName(): string
    {
        return __('integrators.local.sms');
    }

    /**
     * @inheritDoc
     */
    public static function getServiceDescription(): string
    {
        return __('integrators.local.sms.description');
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
        return "sms";
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
        return LocalSmsConnection::class;
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