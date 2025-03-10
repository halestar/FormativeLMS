<?php

namespace Database\Seeders;

use App\Models\People\Address;
use App\Models\People\Person;
use App\Models\People\Phone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class FacultySeeder extends Seeder
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
        // Here we will do 10 faculty, 2 with middle name, 2 with nick names and 2 with both
        try
        {
            Person::factory()
                ->count(4)
                ->faculty()
                ->hasAttached(Address::factory()->count(1), ['primary' => true])
                ->hasAttached(Phone::factory()->mobile()->count(1), ['primary' => true])
                ->create();

            Person::factory()
                ->count(2)
                ->faculty()
                ->nick()
                ->hasAttached(Address::factory()->count(1), ['primary' => true])
                ->hasAttached(Phone::factory()->mobile()->count(1), ['primary' => true])
                ->create();

            Person::factory()
                ->count(2)
                ->faculty()
                ->middleName()
                ->hasAttached(Address::factory()->count(1), ['primary' => true])
                ->hasAttached(Phone::factory()->mobile()->count(1), ['primary' => true])
                ->create();

            Person::factory()
                ->count(2)
                ->faculty()
                ->middleName()
                ->nick()
                ->hasAttached(Address::factory()->count(1), ['primary' => true])
                ->hasAttached(Phone::factory()->mobile()->count(1), ['primary' => true])
                ->create();
        }
        catch(RoleDoesNotExist $e)
        {
            Log::debug($e);
        }
    }
}
