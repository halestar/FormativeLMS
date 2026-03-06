<?php

namespace Database\Factories\Integrations;

use App\Models\Integrations\Integrator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Integrations\Integrator>
 */
class IntegratorFactory extends Factory
{
    protected $model = Integrator::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'className' => Integrator::class,
            'path' => $this->faker->unique()->slug(),
            'description' => $this->faker->sentence(),
            'data' => [],
            'version' => '1.0',
            'enabled' => true,
            'has_personal_connections' => true,
            'has_system_connections' => false,
            'configurable' => false,
        ];
    }
}
