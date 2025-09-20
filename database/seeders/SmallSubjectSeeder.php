<?php

namespace Database\Seeders;

use App\Models\Locations\Campus;
use App\Models\SubjectMatter\Subject;
use Illuminate\Database\Seeder;

class SmallSubjectSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		foreach(Campus::all() as $campus)
		{
			$idx = 0;
			foreach(['English', 'Math', 'Science'] as $subject)
			{
				Subject::create(
					[
						'campus_id' => $campus->id,
						'name' => $subject,
						'color' => fake()->hexColor(),
						'order' => $idx++
					]);
			}
		}
	}
}
