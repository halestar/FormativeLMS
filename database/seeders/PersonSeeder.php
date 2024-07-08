<?php

namespace Database\Seeders;

use App\Models\People\InternalUser;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Role;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //first, we add the admin
        $admin = Person::create(
            [
                'first' => "Admin",
                'middle' => null,
                'last' => "Kalinec",
                'email' => "admin@kalinec.net",
                'nick' => null,
                'pronouns' => 'He/Him',
                'dob' => "1969-06-09",
                'ethnicity' => 'Hispanic',
                'password' => Hash::make("admin"),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        $admin->assignRole(SchoolRoles::$ADMIN);
        $admin->assignRole(SchoolRoles::$STAFF);
        //now we do some people. for a school of 100, we will do 20 faculty, 7 staff, 2 coaches and 70 students, with 100 parents.
        //faculty
        try
        {
            Person::factory()
                ->count(20)
                ->faculty()
                ->create();
        }
        catch(RoleDoesNotExist $e)
        {
            Log::debug($e);
        }
        //staff
        try
        {
            Person::factory()
                ->count(8)
                ->staff()
                ->create();
        }
        catch(RoleDoesNotExist $e){}
        //coaches
        try
        {
            Person::factory()
                ->count(2)
                ->coach()
                ->create();
        }
        catch(RoleDoesNotExist $e){}
        //students
        try
        {
            Person::factory()
                ->count(70)
                ->student()
                ->create();
        }
        catch(RoleDoesNotExist $e){}
        //parents
        try
        {
            Person::factory()
                ->count(100)
                ->parents()
                ->create();
        }
        catch(RoleDoesNotExist $e){}

    }
}
