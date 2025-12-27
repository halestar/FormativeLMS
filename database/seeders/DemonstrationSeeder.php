<?php

namespace Database\Seeders;

use App\Models\People\Person;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\Learning\LearningDemonstration;
use App\Models\SubjectMatter\Learning\LearningDemonstrationClassSession;
use Database\Factories\Learning\DemonstrationClassesFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemonstrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		foreach(Person::teachers()->get() as $faculty)
		{
			//we will create a total of 8 demonstrations per class session.
			//1 active per criteria, and 1 past per criteria.
			foreach ($faculty->currentClassSessions as $session)
			{
				$skills = $session->course->suggestedSkills();
				foreach ($session->classCriteria as $criteria)
				{
					$skillIdx = rand(0, count($skills) - 1);
					LearningDemonstration::factory()
						->count(1)
						->for($faculty, 'owner')
						->hasAttached($skills[$skillIdx], ['rubric' => $skills[$skillIdx]->rubric, 'weight' => 1])
						->has(LearningDemonstrationClassSession::factory()
							->count(1)
							->for($session, 'classSession')
							->for($criteria, 'criteria')
							->post(), 'demonstrationSessions')
						->create();

					$skillIdx = rand(0, count($skills) - 1);
					LearningDemonstration::factory()
						->count(1)
						->for($faculty, 'owner')
						->hasAttached($skills[$skillIdx], ['rubric' => $skills[$skillIdx]->rubric, 'weight' => 1])
						->has(LearningDemonstrationClassSession::factory()
							->count(1)
							->past()
							->for($session, 'classSession')
							->for($criteria, 'criteria')
							->post(), 'demonstrationSessions')
						->create();
				}
			}
		}
    }
}
