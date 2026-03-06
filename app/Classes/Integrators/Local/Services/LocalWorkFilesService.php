<?php

namespace App\Classes\Integrators\Local\Services;

use App\Classes\Integrators\Local\Connections\LocalWorkFilesConnection;
use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\Integrator;
use App\Models\Integrations\LmsIntegrationService;
use App\Models\People\Person;

class LocalWorkFilesService extends LmsIntegrationService
{
    /**
     * {@inheritDoc}
     */
    public static function getServiceType(): IntegratorServiceTypes
    {
        return IntegratorServiceTypes::WORK;
    }

    /**
     * {@inheritDoc}
     */
    public static function getServiceName(): string
    {
        return __('integrators.local.work');
    }

    /**
     * {@inheritDoc}
     */
    public static function getServiceDescription(): string
    {
        return __('integrators.local.work.description');
    }

    /**
     * {@inheritDoc}
     */
    public static function getDefaultData(): array
    {
        return
            [
                'work_disk' => config('lms.storage.work'),
            ];
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
        return 'work';
    }

    public function canEnable(): bool
    {
        return true;
    }

    public function getConnectionClass(): string
    {
        return LocalWorkFilesConnection::class;
    }

    public function canConnect(?Person $person = null): bool
    {
        return $person == null;
    }

    public function canRegister(?Person $person = null): bool
    {
        return false;
    }

    public static function canConfigure(?Person $person = null): bool
    {
        return true;
    }

    public function registrationUrl(?Person $person = null): ?string
    {
        return null;
    }

    public function configurationUrl(?Person $person = null): ?string
    {
        return route(Integrator::INTEGRATOR_ACTION_PREFIX.'local.work.index');
    }
}
