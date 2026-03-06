<?php

namespace App\Classes\Integrators\Local\Services;

use App\Classes\Integrators\Local\Connections\LocalAiConnection;
use App\Classes\Integrators\Local\LocalIntegrator;
use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\Integrator;
use App\Models\Integrations\LmsIntegrationService;
use App\Models\People\Person;
use Illuminate\Support\Facades\Http;

class LocalAiService extends LmsIntegrationService
{
    /**
     * {@inheritDoc}
     */
    public static function getServiceType(): IntegratorServiceTypes
    {
        return IntegratorServiceTypes::AI;
    }

    /**
     * {@inheritDoc}
     */
    public static function getServiceName(): string
    {
        return __('integrators.local.ai');
    }

    /**
     * {@inheritDoc}
     */
    public static function getServiceDescription(): string
    {
        return __('integrators.local.ai.description');
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
        return 'ai';
    }

    public function canEnable(): bool
    {
        return true;
    }

    public function getConnectionClass(): string
    {
        return LocalAiConnection::class;
    }

    public function canConnect(?Person $person = null): bool
    {
        // a person can't ever have a personal connection
        if ($person) {
            return false;
        }

        // To connect to a system connection, they must register first, so they can only connect if they have a connection
        return $this->hasConnection();
    }

    public function canRegister(?Person $person = null): bool
    {
        // Only the system can register a connection
        return $person === null;
    }

    public static function canConfigure(?Person $person = null): bool
    {
        // only the system can configure.
        return $person == null;
    }

    public function registrationUrl(?Person $person = null): ?string
    {
        if ($person) {
            return null;
        }

        return route(Integrator::INTEGRATOR_ACTION_PREFIX.LocalIntegrator::getPath().'.services.ai.config');
    }

    public function configurationUrl(?Person $person = null): ?string
    {
        if (! $person) {
            return route(Integrator::INTEGRATOR_ACTION_PREFIX.LocalIntegrator::getPath().'.services.ai.config');
        }

        return route(Integrator::INTEGRATOR_ACTION_PREFIX.LocalIntegrator::getPath().'.services.ai.config.personal');
    }

    public function testConnection($endpoint): bool
    {
        try {
            $response = Http::get($endpoint);
        } catch (\Exception $e) {
            return false;
        }

        return $response->body() == 'Ollama is running';
    }
}
