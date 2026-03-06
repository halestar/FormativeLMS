<?php

namespace Database\Factories\Integrations;

use App\Models\Integrations\IntegrationConnection;
use App\Models\Integrations\IntegrationService;
use App\Models\People\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Integrations\IntegrationConnection>
 */
class IntegrationConnectionFactory extends Factory
{
    protected $model = IntegrationConnection::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => IntegrationService::factory(),
            'person_id' => Person::factory(),
            'data' => [],
            'className' => IntegrationConnection::class,
            'enabled' => true,
        ];
    }
}
