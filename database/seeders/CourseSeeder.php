<?php

namespace Database\Seeders;

use App\Models\SubjectMatter\Course;
use App\Models\SubjectMatter\Subject;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		foreach(Subject::all() as $subject)
		{
			foreach($subject->campus->levels as $level)
			{
				Course::create(
					[
						'subject_id' => $subject->id,
						'name' => $subject->name . ' - ' . $level->name,
					]);
			}
		}
	}
}
