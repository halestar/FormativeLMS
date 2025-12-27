<?php

namespace Database\Seeders;

use App\Casts\Learning\Rubric;
use App\Console\Commands\DevelopRubrics;
use App\Models\SubjectMatter\Assessment\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class RubricRestoreSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		foreach(Skill::all() as $skill)
		{
			$fname = "backups/develop-rubrics/" . $skill->designation . '.json';
			if(Storage::disk('private')->exists($fname))
			{
				$json = json_decode(Storage::disk('private')->get($fname), true);
				$skill->rubric = Rubric::hydrate($json);
				$skill->active = true;
				$skill->save();
			}
			else
			{
				$skill->active = false;
				$skill->save();
			}
		}
	}
}
