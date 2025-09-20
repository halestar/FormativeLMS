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

class SmallFamilySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
	    /**
	     * For a school of 10, we will do:
	     *   -- 2 faculty
	     *   -- 1 staff
	     *   -- 1 coach
	     *    -- 12 families, which will have 1 students (1 per grade)
	     */

        foreach(Level::all() as $level)
        {
            try
            {
                // Kindergarten
                Person::factory()
                    ->count(1)
                    ->student($level)
                    ->nick()
                    ->hasAttached(Address::factory()->count(1), ['primary' => true])
                    ->hasAttached(Phone::factory()->mobile()->count(1), ['primary' => true])
                    ->hasAttached
                    (
                        Person::factory()
                            ->count(2)
                            ->parents()
                            ->hasAttached(Phone::factory()->mobile()->count(1), ['primary' => false, 'label' => "Work"]),
                        ['relationship_id' => Relationship::CHILD], 'relationships'
                    )
                    ->attachParents()
                    ->sharePrimaryAddress()
                    ->sharePrimaryPhone()
                    ->create();

            }
            catch (RoleDoesNotExist $e)
            {
                Log::debug($e);
            }
        }
    }
}
