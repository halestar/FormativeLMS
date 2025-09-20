<?php

namespace Database\Seeders;

use App\Models\People\Person;
use App\Models\People\StudentRecord;
use Illuminate\Database\Seeder;

class StudentTrackerSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$trackers = Person::staff()->orWhere->coaches()
		                                    ->get();
		//we give every student a teacher as a tracker
		foreach(StudentRecord::all() as $student)
			$student->tracker()
			        ->attach($trackers->random()->id);
	}
}
