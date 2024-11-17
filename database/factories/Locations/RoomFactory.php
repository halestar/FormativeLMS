<?php

namespace Database\Factories\Locations;

use App\Models\Locations\Campus;
use App\Models\Locations\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => (string)($this->faker->numberBetween(100, 499)),
        ];
    }

    public function hasCampuses(): static
    {
        return $this->afterCreating(function (Room $room)
        {
            $room->campuses()->attach(Campus::inRandomOrder()->limit(random_int(1, 3))->get()->pluck('id')->toArray(),
                [
                    'classroom' => true,
                ]);
        });
    }
}
