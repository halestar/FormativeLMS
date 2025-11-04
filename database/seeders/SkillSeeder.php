<?php

namespace Database\Seeders;

use App\Models\Locations\Campus;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\Assessment\SkillCategory;
use App\Models\SystemTables\Level;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class SkillSeeder extends Seeder
{
	public Campus $hs;
	public Campus $ms;
	public Campus $es;
	
	public function __construct()
	{
		$this->hs = Campus::where('abbr', 'HS')
		                  ->first();
		$this->ms = Campus::where('abbr', 'MS')
		                  ->first();
		$this->es = Campus::where('abbr', 'ES')
		                  ->first();
	}
	
	private function subjects($name)
	{
		return
		[
			'hs' => $this->hs->subjects()
			                 ->where('name', $name)
			                 ->first()->id,
			'ms' => $this->ms->subjects()
			                 ->where('name', $name)
			                 ->first()->id,
			'es' => $this->es->subjects()
			                 ->where('name', $name)
			                 ->first()->id,
		];
	}
	
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		//English
		$this->importSkills(storage_path('app/standards/english.csv'), $this->subjects('English'));
		//Math
		$this->importSkills(storage_path('app/standards/math.csv'), $this->subjects('Math'));
		//Science
		$this->importSkills(storage_path('app/standards/science.csv'), $this->subjects('Science'));
		//Social Studies
		$this->importSkills(storage_path('app/standards/social_science.csv'), $this->subjects('Social Studies'));
		//Art
		$this->importSkills(storage_path('app/standards/art.csv'), $this->subjects('Art'));
		//Health
		$this->importSkills(storage_path('app/standards/health.csv'), $this->subjects('Health'));
		//Languages
		$this->importSkills(storage_path('app/standards/language.csv'), $this->subjects('Languages'));
		
		//now we do some global skills
		$skills = [];
		$skills[] = Skill::create(
			[
				'designation' => "CHAR-LATE",
				'name' => 'Lateness',
				'description' => 'The student is able to turn in their work on time.',
				'active' => true,
				'global' => true,
			]);
		$skills[] = Skill::create(
			[
				'designation' => "CHAR-PLAG",
				'name' => 'Plagiarism',
				'description' => 'The student is does not engage in plagiarism',
				'active' => true,
				'global' => true,
			]);
		$skills[] = Skill::create(
			[
				'designation' => "CHAR-ETHIC",
				'name' => 'Ethics',
				'description' => 'The student is abides the the school\'s ethics, as described int he student\'s handbook.',
				'active' => true,
				'global' => true,
			]);
		$levels = Level::all();
		foreach($skills as $skill)
		{
			foreach($levels as $level)
				$skill->levels()->attach($level->id);
		}
	}
	
	public function importSkills(string $fname, array $subjects)
	{
		if($standards = fopen($fname, 'r'))
		{
			//first, we get the headers
			$header = fgetcsv($standards);
			$contentArea = 0;
			$designation = 1;
			$gradeRange = 2;
			$minGrade = 3;
			$maxGrade = 4;
			$description = count($header) - 1;
			$categories = [];
			$catDesignations = [];
			for($i = 5; $i < $description; $i++)
				$catDesignations[] = $header[$i];
			
			$levels = Level::all();
			//next, we cycle through the data
			while($row = fgetcsv($standards))
			{
				//find the campus based on the starting grade
				$startGrade = $row[$minGrade];
				$endGrade = $row[$maxGrade];
				$skill = Skill::create(
					[
						'designation' => $row[$designation],
						'description' => nl2br($row[$description]),
						'active' => true,
					]);
				//next, we attach the grades
				$level_ids = [];
				$subject_ids = [];
				for($i = $startGrade; $i <= $endGrade; $i++)
				{
					$level_ids[] = $levels[$i]->id;
					if($i < 6)
						$subject_ids['es'] = $subjects['es'];
					elseif($i < 9)
						$subject_ids['ms'] = $subjects['ms'];
					elseif($i < 12)
						$subject_ids['hs'] = $subjects['hs'];
				}
				$skill->levels()->attach($level_ids);
				$skill->subjects()->attach(array_values($subject_ids));
				//and the categories
				$ctr = 5;
				foreach($catDesignations as $catDesignation)
				{
					$cat = SkillCategory::where('name', $row[$ctr])
					                    ->first();
					if($cat && $catDesignation)
						$cat->skills()
						    ->attach($skill->id, ['designation' => $catDesignation]);
					else
						Log::error('Could not find category: ' . $row[$ctr] . " or designation: " . $catDesignation);
					$ctr++;
				}
			}
		}
	}
}
