<?php

namespace Database\Seeders;

use App\Models\People\Person;
use Hashids\Hashids;
use Illuminate\Database\Seeder;

class SchoolIdSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$hashids = new Hashids('FabLMS', config('lms.school_id_length'), '0123456789cfhistu');
		foreach(Person::all() as $person)
		{
			$person->school_id = $hashids->encode($person->id);
			$person->save();
		}
	}
}
