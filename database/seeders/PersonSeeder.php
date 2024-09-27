<?php

namespace Database\Seeders;

use App\Models\CRUD\Ethnicity;
use App\Models\CRUD\Gender;
use App\Models\CRUD\Pronouns;
use App\Models\CRUD\Title;
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
                'ethnicity_id' => Ethnicity::where('name', 'LIKE', 'Hispanic or Latino')->first()->id,
                'title_id' => Title::where('name', 'LIKE', 'Mr.')->first()->id,
                'gender_id' => Gender::where('name', 'LIKE', 'Male')->first()->id,
                'pronoun_id' => Pronouns::where('name', 'LIKE', 'He/Him')->first()->id,
                'occupation' => 'IT',
                'job_title' => 'Director of Technology',
                'work_company' => 'New Roads School',
                'portrait_url' => 'https://storage.googleapis.com/deep-citizen-425500-e0.appspot.com/cms/xTaR4dVm43h9gJT4sn7v9zjcp9vTnYKkNHIhVXSr.jpg',
            ]);
        $admin->assignRole(SchoolRoles::$ADMIN);
        $admin->assignRole(SchoolRoles::$EMPLOYEE);
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
        $staff->assignRole(SchoolRoles::$EMPLOYEE);
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
        $staff->assignRole(SchoolRoles::$EMPLOYEE);
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
        $staff->assignRole(SchoolRoles::$EMPLOYEE);
        //now we do some people. for a school of 50, we will do 10 faculty, 4 staff, 1 coaches and 35 students, with 50 parents.
        //faculty
        try
        {
            Person::factory()
                ->count(10)
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
                ->count(4)
                ->staff()
                ->create();
        }
        catch(RoleDoesNotExist $e){}
        //coaches
        try
        {
            Person::factory()
                ->count(1)
                ->coach()
                ->create();
        }
        catch(RoleDoesNotExist $e){}
        //students
        try
        {
            Person::factory()
                ->count(35)
                ->student()
                ->create();
        }
        catch(RoleDoesNotExist $e){}
        //parents
        try
        {
            Person::factory()
                ->count(50)
                ->parents()
                ->create();
        }
        catch(RoleDoesNotExist $e){}

    }
}
