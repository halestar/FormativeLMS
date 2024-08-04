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
        //first, we add the super admin
        $admin = Person::create(
            [
                'first' => "Admin",
                'middle' => null,
                'last' => "Kalinec",
                'email' => config('lms.superadmin_email'),
                'nick' => null,
                'dob' => "1969-06-09",
                'password' => Hash::make(config('lms.superadmin_password')),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        $admin->assignRole(SchoolRoles::$ADMIN);
        $admin->assignRole(SchoolRoles::$STAFF);

        //now we make some other fake people for testing.
        $staff = Person::create(
            [
                'first' => "Staff",
                'middle' => null,
                'last' => "Kalinec",
                'email' => 'staff@kalinec.net',
                'nick' => null,
                'dob' => "1969-06-09",
                'password' => Hash::make('staff'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        $staff->assignRole(SchoolRoles::$STAFF);
        $faculty = Person::create(
            [
                'first' => "Faculty",
                'middle' => null,
                'last' => "Kalinec",
                'email' => 'faculty@kalinec.net',
                'nick' => null,
                'dob' => "1969-06-09",
                'password' => Hash::make('faculty'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        $faculty->assignRole(SchoolRoles::$FACULTY);
        $student = Person::create(
            [
                'first' => "Student",
                'middle' => null,
                'last' => "Kalinec",
                'email' => 'student@kalinec.net',
                'nick' => null,
                'dob' => "2010-06-09",
                'password' => Hash::make('student'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        $student->assignRole(SchoolRoles::$STUDENT);
        $parent = Person::create(
            [
                'first' => "Parent",
                'middle' => null,
                'last' => "Kalinec",
                'email' => 'parent@kalinec.net',
                'nick' => null,
                'dob' => "2010-06-09",
                'password' => Hash::make('parent'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        $parent->assignRole(SchoolRoles::$PARENT);
        $coach = Person::create(
            [
                'first' => "Parent",
                'middle' => null,
                'last' => "Kalinec",
                'email' => 'coach@kalinec.net',
                'nick' => null,
                'dob' => "2010-06-09",
                'password' => Hash::make('coach'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        $coach->assignRole(SchoolRoles::$COACH);
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
