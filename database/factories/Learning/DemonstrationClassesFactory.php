<?php

namespace Database\Factories\Learning;

use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Learning\LearningDemonstrationClassSession;
use App\Models\SubjectMatter\Learning\LearningDemonstrationOpportunity;
use App\Models\SubjectMatter\Learning\LearningDemonstrationOpportunityAssessment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Log;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=App\Models\SubjectMatter\Learning\LearningDemonstrationClassSession>
 */
class DemonstrationClassesFactory extends Factory
{
	protected $model = LearningDemonstrationClassSession::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return
        [
            'criteria_weight' => 1,
	        'posted_on' => now()->subDays(rand(1, 5)),
	        'due_on' => now()->addDays(rand(1, 7)),
        ];
    }

	public function past()
	{
		return $this->state(fn(array $attributes) => [
			'posted_on' => Carbon::now()->subDays(rand(8, 16)),
			'due_on' => Carbon::now()->subDays(rand(1, 7)),
		]);
	}

	public function post(): Factory
	{
		return $this->afterCreating(function(LearningDemonstrationClassSession $ldSession)
		{
			$students = $ldSession->classSession->students->pluck('name', 'id')->toArray();
			foreach($students as $studentId => $studentName)
			{
				$lo = LearningDemonstrationOpportunity::create(
				[
					'demonstration_session_id' => $ldSession->id,
					'student_id' => $studentId,
					'posted_on' => $ldSession->posted_on,
					'due_on' => $ldSession->due_on,
					'criteria_weight' => $ldSession->criteria_weight,
				]);
				foreach($ldSession->demonstration->skills as $skill)
				{
					$loAssessment = LearningDemonstrationOpportunityAssessment::create(
					[
						'opportunity_id' => $lo->id,
						'skill_id' => $skill->id,
						'rubric' => $skill->rubric->createAssessment(),
						'weight' => 1,
					]);
				}
			}
		});
	}
}
