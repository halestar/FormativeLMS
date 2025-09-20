<?php

namespace Database\Seeders;

use App\Models\CRUD\Level;
use App\Models\CRUD\Relationship;
use App\Models\People\Address;
use App\Models\People\Person;
use App\Models\People\Phone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class FamilySeeder extends Seeder
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
		 *   -- 65 families, which will have 65 students (5 per grade)
		 *
		 * We will be doing 5 students per grade (not implemented atm), so we
		 * will be doing each one separated by grade. We also need to test parents
		 * having multiple kids (both in the same grade and in different ones)
		 * and students having 1-4 parents (with step parents and grandparents), all the
		 * students should have nicknames, but only some should have middle names.
		 */
		
		foreach(Level::all() as $level)
		{
			try
			{
				// Kindergarten
				Person::factory()
				      ->count(5)
				      ->student($level)
				      ->nick()
				      ->hasAttached(Address::factory()
				                           ->count(1), ['primary' => true])
				      ->hasAttached(Phone::factory()
				                         ->mobile()
				                         ->count(1), ['primary' => true])
				      ->hasAttached
				      (
					      Person::factory()
					            ->count(2)
					            ->parents()
					            ->hasAttached(Phone::factory()
					                               ->mobile()
					                               ->count(1), ['primary' => false, 'label' => "Work"]),
					      ['relationship_id' => Relationship::CHILD], 'relationships'
				      )
				      ->attachParents()
				      ->sharePrimaryAddress()
				      ->sharePrimaryPhone()
				      ->create();
				
			}
			catch(RoleDoesNotExist $e)
			{
				Log::debug($e);
			}
		}
	}
}
