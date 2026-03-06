<?php

namespace Tests\Feature\Integrations;

use App\Classes\Integrators\Local\Connections\LocalAuthConnection;
use App\Classes\Integrators\Local\LocalIntegrator;
use App\Classes\Integrators\Local\Services\LocalAuthService;
use App\Models\Integrations\IntegrationConnection;
use App\Models\Integrations\IntegrationService;
use App\Models\Integrations\Integrator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CoreIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that Integrator model correctly instantiates subclasses based on className.
     */
    public function test_integrator_polymorphism(): void
    {
        $integrator = Integrator::factory()->create([
            'className' => LocalIntegrator::class,
        ]);

        $retrieved = Integrator::find($integrator->id);

        $this->assertInstanceOf(LocalIntegrator::class, $retrieved);
        $this->assertEquals(LocalIntegrator::class, $retrieved->className);
    }

    /**
     * Test that IntegrationService model correctly instantiates subclasses based on className.
     */
    public function test_service_polymorphism(): void
    {
        $service = IntegrationService::factory()->create([
            'className' => LocalAuthService::class,
        ]);

        $retrieved = IntegrationService::find($service->id);

        $this->assertInstanceOf(LocalAuthService::class, $retrieved);
        $this->assertEquals(LocalAuthService::class, $retrieved->className);
    }

    /**
     * Test that IntegrationConnection model correctly instantiates subclasses based on className.
     */
    public function test_connection_polymorphism(): void
    {
        $service = IntegrationService::factory()->create([
            'className' => LocalAuthService::class,
            'data' => [
                'maxAttempts' => 5,
                'decayMinutes' => 1,
            ],
        ]);

        $connection = IntegrationConnection::factory()->create([
            'service_id' => $service->id,
            'className' => LocalAuthConnection::class,
        ]);

        // IntegrationConnection is a Pivot, but it can be queried like a model if it has its own table
        $retrieved = IntegrationConnection::where('id', $connection->id)->first();

        $this->assertInstanceOf(LocalAuthConnection::class, $retrieved);
        $this->assertEquals(LocalAuthConnection::class, $retrieved->className);
    }

    /**
     * Test relationship between Integrator and Services.
     */
    public function test_integrator_services_relationship(): void
    {
        $integrator = Integrator::factory()->create([
            'className' => LocalIntegrator::class,
        ]);

        $service = IntegrationService::factory()->create([
            'integrator_id' => $integrator->id,
            'className' => LocalAuthService::class,
        ]);

        $this->assertCount(1, $integrator->services);
        $this->assertInstanceOf(LocalAuthService::class, $integrator->services->first());
    }
}
