<?php

namespace Database\Factories\SubjectMatter;

use App\Models\SubjectMatter\Assessment\Skill;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

#[UseModel(Skill::class)]
class SkillFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		return
			[
				'designation' => fake()->word(),
				'name'        => fake()->words(3),
				'description' => fake()->text(1024),
				'rubric'      => null,
				'global'      => false,
				'active'      => false,
			];
	}
}
