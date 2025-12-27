<?php

namespace Database\Factories\Learning;

use App\Models\Locations\Year;
use App\Models\SubjectMatter\Learning\LearningDemonstration;
use App\Models\SubjectMatter\Learning\LearningDemonstrationClassSession;
use App\Models\SubjectMatter\Learning\LearningDemonstrationOpportunity;
use App\Models\SubjectMatter\Learning\LearningDemonstrationOpportunityAssessment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=App\Models\SubjectMatter\Learning\LearningDemonstration>
 */
class DemonstrationFactory extends Factory
{
	protected $model = LearningDemonstration::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return
        [
            "type_id" => 1,
	        "year_id" => Year::currentYear()->id,
	        "name" => $this->faker->sentence(3),
	        "abbr" => $this->faker->word,
	        "demonstration" => $this->faker->paragraphs(5, true),
        ];
    }

	public function withLinks(array $links): static
	{
		return $this->state(fn(array $attributes) => [
			'links' => $links,
		]);
	}

	public function withQuestions(array $questions): static
	{
		return $this->state(fn(array $attributes) => ['questions' => $questions]);
	}

	public function skills(array $skills): Factory
	{
		return $this->afterCreating(function(LearningDemonstration $demonstration) use ($skills)
		{
			$payload = [];
			foreach($skills as $skill)
				$payload[$skill->id] = ['rubric' => $skill->rubric, 'weight' => 1];
			$demonstration->skills()->sync($payload);
		});
	}

}
