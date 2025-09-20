<?php

namespace Database\Factories\People;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AddressFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		return [
			'line1' => $this->faker->streetAddress(),
			'line2' => null,
			'line3' => null,
			'city' => $this->faker->city(),
			'state' => $this->faker->stateAbbr(),
			'zip' => $this->faker->postcode(),
			'country' => config('lms.default_country'),
		];
	}
	
	public function hasLine2(): static
	{
		return $this->state(fn(array $attributes) => [
			'line2' => $this->faker->secondaryAddress(),
		]);
	}
	
	public function foreign(): static
	{
		return $this->state(fn(array $attributes) => [
			'line2' => $this->faker->secondaryAddress(),
			'line3' => $this->faker->city(),
			'country' => $this->faker->countryCode(),
		]);
	}
}
