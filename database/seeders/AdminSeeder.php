<?php

namespace Database\Seeders;

use App\Models\CRUD\Relationship;
use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Seeder;

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
	            'first' => config('seeder.admin_first'),
                'middle' => null,
	            'last' => config('seeder.admin_last'),
	            'email' => config('seeder.admin_email'),
                'nick' => null,
                'dob' => "1969-06-09",
	            'portrait_url' => config('seeder.admin_portrait'),
                'school_id' => 1,
	            'auth_driver' => config('seeder.admin_auth'),
            ]);
		$admin->refresh();
        $admin->assignRole(SchoolRoles::$ADMIN);
        $admin->assignRole(SchoolRoles::$EMPLOYEE);
        $admin->assignRole(SchoolRoles::$STAFF);
	    if(config('seeder.admin_auth') == "local")
		    $admin->auth_driver->setPassword(config('seeder.admin_pass'));


        //now we make some other fake people for testing.
        $staff = Person::create(
            [
                'first' => "Staff",
                'middle' => null,
                'last' => "Kalinec",
                'email' => 'staff@kalinec.net',
                'nick' => null,
                'dob' => "1969-06-09",
                'portrait_url' => env('APP_URL').'/storage/idpics/2.jpg',
                'thumbnail_url' => env('APP_URL').'/storage/idpics/2.jpg',
                'school_id' => 2,
	            'auth_driver' => 'local',
            ]);
	    $staff->refresh();
        $staff->assignRole(SchoolRoles::$EMPLOYEE);
        $staff->assignRole(SchoolRoles::$STAFF);
        $staff->assignRole("DB Editor");
        $staff->assignRole("Academic Manager");
        $staff->assignRole("Locations Manager");
        $staff->assignRole("Schedule Manager");
	    $staff->auth_driver->setPassword('staff');



        $faculty = Person::create(
            [
                'first' => "Faculty",
                'middle' => null,
                'last' => "Kalinec",
                'email' => 'faculty@kalinec.net',
                'nick' => null,
                'dob' => "1969-06-09",
                'portrait_url' => env('APP_URL').'/storage/idpics/3.jpg',
                'thumbnail_url' => env('APP_URL').'/storage/idpics/3.jpg',
                'school_id' => 3,
	            'auth_driver' => 'local',
            ]);
	    $faculty->refresh();
        $faculty->assignRole(SchoolRoles::$FACULTY);
        $faculty->assignRole(SchoolRoles::$EMPLOYEE);
	    $faculty->auth_driver->setPassword('faculty');


        $coach = Person::create(
            [
                'first' => "Coach",
                'middle' => null,
                'last' => "Kalinec",
                'email' => 'coach@kalinec.net',
                'nick' => null,
                'dob' => "2010-06-09",
                'portrait_url' => env('APP_URL').'/storage/idpics/4.jpg',
                'thumbnail_url' => env('APP_URL').'/storage/idpics/4.jpg',
                'school_id' => 4,
	            'auth_driver' => 'local',
            ]);
	    $coach->refresh();
        $coach->assignRole(SchoolRoles::$COACH);
        $coach->assignRole(SchoolRoles::$EMPLOYEE);
	    $coach->auth_driver->setPassword('coach');

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
                'portrait_url' => env('APP_URL').'/storage/idpics/5.jpg',
                'thumbnail_url' => env('APP_URL').'/storage/idpics/5.jpg',
                'school_id' => 5,
	            'auth_driver' => 'local',
            ]);
	    $student->refresh();
        $student->assignRole(SchoolRoles::$STUDENT);
	    $student->auth_driver->setPassword('student');
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
                'portrait_url' => env('APP_URL').'/storage/idpics/6.jpg',
                'thumbnail_url' => env('APP_URL').'/storage/idpics/6.jpg',
                'school_id' => 6,
	            'auth_driver' => 'local',
            ]);
	    $parent->refresh();
        $parent->assignRole(SchoolRoles::$PARENT);
	    $parent->auth_driver->setPassword('parent');
        //next, we assign the bi-directinal child-parent relationship between the parent and child accounts.
        $student->relationships()->attach($parent->id, ['relationship_id' => Relationship::CHILD]);
        $parent->relationships()->attach($student->id, ['relationship_id' => Relationship::PARENT]);
	    
	    //finally, we add additional admins
	    $i = 1;
	    while(config('seeder.admin' . $i . ".email", false))
	    {
		    $admin = Person::create(
			    [
				    'first' => config('seeder.admin' . $i . ".first"),
				    'middle' => null,
				    'last' => config('seeder.admin' . $i . ".last"),
				    'email' => config('seeder.admin' . $i . ".email"),
				    'nick' => null,
				    'dob' => "1969-06-09",
				    'school_id' => ($i + 7),
				    'auth_driver' => config('seeder.admin' . $i . ".auth"),
			    ]);
		    $admin->refresh();
		    $admin->assignRole(SchoolRoles::$ADMIN);
		    $admin->assignRole(SchoolRoles::$EMPLOYEE);
		    $admin->assignRole(SchoolRoles::$STAFF);
		    if(config('seeder.admin' . $i . ".auth") == "local")
			    $admin->auth_driver->setPassword(config('seeder.admin' . $i . ".auth"));
		    $i++;
	    }

    }
}
