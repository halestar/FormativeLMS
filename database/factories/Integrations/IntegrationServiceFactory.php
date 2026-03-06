<?php

namespace Database\Factories\Integrations;

use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\IntegrationService;
use App\Models\Integrations\Integrator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Integrations\IntegrationService>
 */
class IntegrationServiceFactory extends Factory
{
    protected $model = IntegrationService::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'integrator_id' => Integrator::factory(),
            'name' => $this->faker->word(),
            'className' => IntegrationService::class,
            'path' => $this->faker->slug(),
            'description' => $this->faker->sentence(),
            'service_type' => $this->faker->randomElement(IntegratorServiceTypes::values()),
            'data' => [],
            'enabled' => true,
            'can_connect_to_people' => true,
            'can_connect_to_system' => false,
            'configurable' => false,
            'inherit_permissions' => true,
        ];
    }
}
