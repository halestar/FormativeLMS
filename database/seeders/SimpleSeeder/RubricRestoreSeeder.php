<?php

namespace Database\Seeders\SimpleSeeder;

use App\Casts\Learning\Rubric;
use App\Models\SubjectMatter\Assessment\Skill;
use Illuminate\Database\Seeder;

class RubricRestoreSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		foreach (Skill::all() as $skill)
		{
			$path = database_path('data/develop-rubrics/' . $skill->designation . '.json');

			if (file_exists($path))
			{
				$json = json_decode(file_get_contents($path), true);
				$skill->rubric = Rubric::hydrate($json);
				$skill->active = true;
			}
			else
				$skill->active = false;
			$skill->save();
		}
	}
}
