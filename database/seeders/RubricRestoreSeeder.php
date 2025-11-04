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
		foreach(Storage::disk('private')
		               ->files(DevelopRubrics::$backupDirectory) as $file)
		{
			$skill = Skill::where('designation', basename($file, '.json'))
			              ->first();
			if($skill)
			{
				$json = json_decode(Storage::disk('private')
				                           ->get($file), true);
				$rubric = Rubric::hydrate($json);
				$skill->rubric = $rubric;
				$skill->save();
			}
		}
	}
}
