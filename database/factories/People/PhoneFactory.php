<?php

namespace Database\Factories\People;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\People\Phone>
 */
class PhoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phone' => $this->faker->phoneNumber(),
            'ext' => null,
            'mobile' => false,
        ];
    }

    public function withExt(): static
    {
        return $this->state(fn (array $attributes) =>
        [
            'ext' => $this->faker->numberBetween(100, 999)
        ]);
    }

    public function mobile(): static
    {
        return $this->state(fn (array $attributes) =>
        [
            'mobile' => true
        ]);
    }

    public function extensionOnly(): static
    {
        return $this->state(fn (array $attributes) =>
        [
            'phone' => $this->faker->numberBetween(100, 999),
        ]);
    }
}
