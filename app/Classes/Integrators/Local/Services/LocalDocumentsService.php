<?php

namespace App\Classes\Integrators\Local\Services;

use App\Classes\Integrators\Local\Connections\LocalDocumentsConnection;
use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\Integrator;
use App\Models\Integrations\LmsIntegrationService;
use App\Models\People\Person;

class LocalDocumentsService extends LmsIntegrationService
{
    public static function getServiceType(): IntegratorServiceTypes
    {
        return IntegratorServiceTypes::DOCUMENTS;
    }

    public static function getServiceName(): string
    {
        return __('integrators.local.documents');
    }

    public static function getServiceDescription(): string
    {
        return __('integrators.local.documents.description');
    }

    public static function getDefaultData(): array
    {
        return
            [
                'documents_disk' => config('lms.storage.documents'),
            ];
    }

    public static function canConnectToPeople(): bool
    {
        return true;
    }

    public static function canConnectToSystem(): bool
    {
        return false;
    }

    public static function getPath(): string
    {
        return 'documents';
    }

    public function canEnable(): bool
    {
        return true;
    }

    public function getConnectionClass(): string
    {
        return LocalDocumentsConnection::class;
    }

    public function canConnect(?Person $person = null): bool
    {
        return true;
    }

    public function canRegister(?Person $person = null): bool
    {
        return false;
    }

    public function canConfigure(?Person $person = null): bool
    {
        return ($person == null);
    }

    public function registrationUrl(?Person $person = null): ?string
    {
        return null;
    }

    public function configurationUrl(?Person $person = null): ?string
    {
        return ($person == null)? route(Integrator::INTEGRATOR_ACTION_PREFIX.'local.documents.index'): null;
    }
}
