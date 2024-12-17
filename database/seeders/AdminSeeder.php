<?php

namespace Database\Seeders;

use App\Models\CRUD\Ethnicity;
use App\Models\CRUD\Gender;
use App\Models\CRUD\Pronouns;
use App\Models\CRUD\Relationship;
use App\Models\CRUD\Title;
use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\People\InternalUser;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
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
                'portrait_url' => 'https://storage.googleapis.com/deep-citizen-425500-e0.appspot.com/cms/3366701.png',
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


        $coach = Person::create(
            [
                'first' => "Coach",
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

        //to the admin accounts, we add all the campuses
        foreach(Campus::all() as $campus)
        {
            $admin->employeeCampuses()->attach($campus);
            $staff->employeeCampuses()->attach($campus);
            $faculty->employeeCampuses()->attach($campus);
            $coach->employeeCampuses()->attach($campus);
        }

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
        //to this student, we assign a student role of a 9th grader at the HS campus
        $studentRecord = StudentRecord::create(
            [
                'campus_id' => 1,
                'person_id' => $student->id,
                'year_id' => 1,
                'level_id' => 4,
                'start_date' => Year::find(1)->year_start,
            ]);

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
        //next, we assign the bi-directinal child-parent relationship between the parent and child accounts.
        $student->relationships()->attach($parent->id, ['relationship_id' => Relationship::CHILD]);
        $parent->relationships()->attach($student->id, ['relationship_id' => Relationship::PARENT]);

    }
}
