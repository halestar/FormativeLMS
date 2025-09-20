<?php

namespace Database\Seeders;

use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\SubjectMatter\SchoolClass;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$year = Year::currentYear();
		foreach(Campus::all() as $campus)
		{
			foreach($campus->courses as $course)
			{
				SchoolClass::create(
					[
						'course_id' => $course->id,
						'year_id' => $year->id,
					]);
			}
		}
	}
}
