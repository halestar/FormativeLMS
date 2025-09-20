<?php

namespace Database\Seeders;

use App\Models\People\Address;
use App\Models\People\Person;
use App\Models\People\Phone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class StaffSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		/**
		 * For a school of 50, we will do:
		 *   -- 10 faculty
		 *   -- 4 staff
		 *   -- 1 coach
		 *    -- 65 families, which will have 65 students (5 per grade)
		 */
		// Here we will do 4 faculty, 1 with middle name, 1 with nick names and 1 with both
		try
		{
			Person::factory()
			      ->count(1)
			      ->staff()
			      ->hasAttached(Address::factory()
			                           ->count(1), ['primary' => true])
			      ->hasAttached(Phone::factory()
			                         ->mobile()
			                         ->count(1), ['primary' => true])
			      ->create();
			Person::factory()
			      ->count(1)
			      ->staff()
			      ->nick()
			      ->hasAttached(Address::factory()
			                           ->count(1), ['primary' => true])
			      ->hasAttached(Phone::factory()
			                         ->mobile()
			                         ->count(1), ['primary' => true])
			      ->create();
			Person::factory()
			      ->count(1)
			      ->staff()
			      ->middleName()
			      ->hasAttached(Address::factory()
			                           ->count(1), ['primary' => true])
			      ->hasAttached(Phone::factory()
			                         ->mobile()
			                         ->count(1), ['primary' => true])
			      ->create();
			Person::factory()
			      ->count(1)
			      ->staff()
			      ->middleName()
			      ->nick()
			      ->hasAttached(Address::factory()
			                           ->count(1), ['primary' => true])
			      ->hasAttached(Phone::factory()
			                         ->mobile()
			                         ->count(1), ['primary' => true])
			      ->create();
		}
		catch(RoleDoesNotExist $e)
		{
			Log::debug($e);
		}
	}
}
