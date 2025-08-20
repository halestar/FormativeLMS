<?php

namespace Database\Seeders;

use App\Casts\Rubric;
use App\Console\Commands\DevelopRubrics;
use App\Models\SubjectMatter\Assessment\KnowledgeSkill;
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
		               ->files(DevelopRubrics::$backupDirectory) as $file) {
			$skill = KnowledgeSkill::where('designation', basename($file, '.json'))
			                       ->first();
			if($skill) {
				$json = json_decode(Storage::disk('private')
				                           ->get($file), true);
				$rubric = Rubric::hydrate($json);
				$skill->setRubric($rubric);
				$skill->save();
			}
		}
	}
}
