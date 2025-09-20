<?php

namespace Database\Seeders;

use App\Models\CRUD\Level;
use App\Models\SubjectMatter\ClassSession;
use Illuminate\Database\Seeder;

class StudentEnrollmentSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		foreach(ClassSession::all() as $session)
		{
			$course = $session->course;
			$campus = $course->campus;
			$grade = explode(" - ", $course->name)[1];
			$level = Level::where('name', $grade)
			              ->first();
			$session->students()
			        ->attach($campus->students()
			                        ->where('level_id', $level->id)
			                        ->get()
			                        ->pluck('id')
			                        ->toArray());
		}
	}
}
