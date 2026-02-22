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
        return 'Local AI Service';
    }

    /**
     * {@inheritDoc}
     */
    public static function getServiceDescription(): string
    {
        return 'A local AI service for testing and development purposes.';
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
        return true;
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

    /**
     * {@inheritDoc}
     */
    public static function canBeConfigured(): bool
    {
        return true;
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

    /**
     * {@inheritDoc}
     */
    public function canConnect(Person $person): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getConnectionClass(): string
    {
        return LocalAiConnection::class;
    }

    /**
     * {@inheritDoc}
     */
    public function canSystemConnect(): bool
    {
        /*
         * It seems like a chicken and the egg problem, where if there isn't a connection established, then
         * it won't let you connect, but how do you establish a connection if this needs to return true?
         * Simple, this forces the connection to have to be created manually, and the only code that has this is
         * is delegated to is the 3-rd partyy code. Thus, they will need to register the system service, mainly
         * through the settings URL.
         */
        return $this->hasSystemConnection() != null;
    }

    /**
     * {@inheritDoc}
     */
    public function getSystemConnectionClass(): string
    {
        return LocalAiConnection::class;
    }

    /**
     * {@inheritDoc}
     */
    public function canRegister(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function registrationUrl(): string
    {
        return route(Integrator::INTEGRATOR_ACTION_PREFIX.LocalIntegrator::getPath().'.services.ai.preferences.personal');
    }

    /**
     * {@inheritDoc}
     */
    public function systemAutoconnect(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function configurationUrl(): string
    {
        return route(Integrator::INTEGRATOR_ACTION_PREFIX.LocalIntegrator::getPath().'.services.ai.preferences');
    }

    /**
     * {@inheritDoc}
     */
    public function canEnable(): bool
    {
        return $this->canSystemConnect();
    }
}
