<?php

namespace Database\Factories\Learning;

use App\Models\SubjectMatter\Learning\ClassCriteria;
use App\Models\SubjectMatter\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=App\Models\SubjectMatter\Learning\ClassCriteria>
 */
class CriteriaFactory extends Factory
{

	protected $model = ClassCriteria::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return
        [
            'name' => $this->faker->word,
	        'abbreviation' => $this->faker->lexify,
        ];
    }

	public function configure(): static
	{
		return $this->afterCreating(function (ClassCriteria $criteria)
		{
			$criteria->sessions()->syncWithPivotValues($criteria->schoolClass->sessions->pluck('id')->toArray(), ['weight' => 1]);
		});
	}


}
