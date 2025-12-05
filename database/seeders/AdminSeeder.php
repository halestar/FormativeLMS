<?php

namespace Database\Seeders;

use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\IntegrationService;
use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\SystemTables\Level;
use App\Models\SystemTables\Relationship;
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
		//is there an auth set?
		if(config('seeder.admin_auth', false))
		{
			$authService = IntegrationService::select('integration_services.*')
			                                 ->join('integrators', 'integrators.id', '=',
				                                 'integration_services.integrator_id')
			                                 ->where('integrators.path', config('seeder.admin_auth'))
			                                 ->where('integration_services.service_type',
				                                 IntegratorServiceTypes::AUTHENTICATION)
			                                 ->first();
		}
		$admin = Person::create(
			[
				'first' => config('seeder.admin_first'),
				'middle' => null,
				'last' => config('seeder.admin_last'),
				'email' => config('seeder.admin_email'),
				'nick' => null,
				'dob' => "1969-06-09",
				'school_id' => 1,
			]);
		$admin->refresh();
		$admin->portrait_url = config('app.url') . '/storage/idpics/1.jpg';
		$admin->thumbnail_url = config('app.url') . '/storage/idpics/1.jpg';
		$admin->save();
		$admin->assignRole([SchoolRoles::$ADMIN, SchoolRoles::$EMPLOYEE, SchoolRoles::$STAFF]);
		if(config('seeder.admin_password', false) && $authService)
			if(($connection = $authService->connect($admin)))
			{
				$connection->setPassword(config('seeder.admin_password'));
				$admin->authConnection()
				      ->associate($connection);
				$admin->save();
			}
		
		//for the following we will be using the local auth driver.
		$authService = IntegrationService::select('integration_services.*')
		                                 ->join('integrators', 'integrators.id', '=',
			                                 'integration_services.integrator_id')
		                                 ->where('integrators.path', 'local')
		                                 ->where('integration_services.service_type',
			                                 IntegratorServiceTypes::AUTHENTICATION)
		                                 ->first();
		//now we make some other fake people for testing.
		$staff = Person::create(
			[
				'first' => "Staff",
				'middle' => null,
				'last' => "Kalinec",
				'email' => 'staff@kalinec.net',
				'nick' => null,
				'dob' => "1969-06-09",
				'school_id' => 2,
			]);
		$staff->refresh();
		$staff->portrait_url = config('app.url') . '/storage/idpics/2.jpg';
		$staff->thumbnail_url = config('app.url') . '/storage/idpics/2.jpg';
		$staff->save();
		$staff->assignRole(
			[
				SchoolRoles::$EMPLOYEE,
				SchoolRoles::$STAFF,
				"DB Editor",
				"Academic Manager",
				"Locations Manager",
				"Schedule Manager",
				"School Manager",
				"Impersonator",
				"Person Contact Editor",
				"Student Tracker",
				"Skills Administrator"
			]);
		if($authService)
			if($connection = $authService->connect($staff))
			{
				$connection->setPassword('staff');
				$staff->authConnection()
				      ->associate($connection);
				$staff->save();
			}
		
		
		$faculty = Person::create(
			[
				'first' => "Faculty",
				'middle' => null,
				'last' => "Kalinec",
				'email' => 'faculty@kalinec.net',
				'nick' => null,
				'dob' => "1969-06-09",
				'school_id' => 3,
			]);
		$faculty->refresh();
		$faculty->portrait_url = config('app.url') . '/storage/idpics/3.jpg';
		$faculty->thumbnail_url = config('app.url') . '/storage/idpics/3.jpg';
		$faculty->save();
		$faculty->assignRole([SchoolRoles::$EMPLOYEE, SchoolRoles::$FACULTY]);
		if($authService)
			if($connection = $authService->connect($faculty))
			{
				$connection->setPassword('faculty');
				$faculty->authConnection()
				        ->associate($connection);
				$faculty->save();
			}
		
		
		$coach = Person::create(
			[
				'first' => "Coach",
				'middle' => null,
				'last' => "Kalinec",
				'email' => 'coach@kalinec.net',
				'nick' => null,
				'dob' => "2010-06-09",
				'school_id' => 4,
			]);
		$coach->refresh();
		$coach->portrait_url = config('app.url') . '/storage/idpics/4.jpg';
		$coach->thumbnail_url = config('app.url') . '/storage/idpics/4.jpg';
		$coach->assignRole([SchoolRoles::$COACH, SchoolRoles::$EMPLOYEE]);
		if($authService)
			if($connection = $authService->connect($coach))
			{
				$connection->setPassword('coach');
				$coach->authConnection()
				      ->associate($connection);
				$coach->save();
			}
		
		//to the admin accounts, we add all the campuses
		foreach(Campus::all() as $campus)
		{
			$admin->employeeCampuses()
			      ->attach($campus);
			$staff->employeeCampuses()
			      ->attach($campus);
			$faculty->employeeCampuses()
			        ->attach($campus);
			$coach->employeeCampuses()
			      ->attach($campus);
		}
		
		$student = Person::create(
			[
				'first' => "Student",
				'middle' => null,
				'last' => "Kalinec",
				'email' => 'student@kalinec.net',
				'nick' => null,
				'dob' => "2010-06-09",
				'school_id' => 5,
			]);
		$student->refresh();
		$student->portrait_url = config('app.url') . '/storage/idpics/5.jpg';
		$student->thumbnail_url = config('app.url') . '/storage/idpics/5.jpg';
		$student->save();
		$student->assignRole(SchoolRoles::$STUDENT);
		if($authService)
			if($connection = $authService->connect($student))
			{
				$connection->setPassword('student');
				$student->authConnection()
				        ->associate($connection);
				$student->save();
			}
		//to this student, we assign a student role of a 9th grader at the HS campus
        $level = Level::where('name', '9th')->first();
		$studentRecord = StudentRecord::create(
			[
				'campus_id' => 1,
				'person_id' => $student->id,
				'year_id' => 1,
				'level_id' => $level->id,
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
				'school_id' => 6,
			]);
		$parent->refresh();
		$parent->portrait_url = config('app.url') . '/storage/idpics/6.jpg';
		$parent->thumbnail_url = config('app.url') . '/storage/idpics/6.jpg';
		$parent->save();
		$parent->assignRole(SchoolRoles::$PARENT);
		if($authService)
			if($connection = $authService->connect($parent))
			{
				$connection->setPassword('parent');
				$parent->authConnection()
				       ->associate($connection);
				$parent->save();
			}
		//next, we assign the bi-directinal child-parent relationship between the parent and child accounts.
		$student->relationships()
		        ->attach($parent->id, ['relationship_id' => Relationship::CHILD]);
		$parent->relationships()
		       ->attach($student->id, ['relationship_id' => Relationship::PARENT]);
		
		//finally, we add additional admins
		$i = 1;
		while(config('seeder.admin' . $i . ".email", false))
		{
			if(config('seeder.admin' . $i . '.auth', false))
			{
				$authService = IntegrationService::select('integration_services.*')
				                                 ->join('integrators', 'integrators.id', '=',
					                                 'integration_services.integrator_id')
				                                 ->where('integrators.path', config('seeder.admin' . $i . '.auth'))
				                                 ->where('integration_services.service_type',
					                                 IntegratorServiceTypes::AUTHENTICATION)
				                                 ->first();
			}
			$admin = Person::create(
				[
					'first' => config('seeder.admin' . $i . ".first"),
					'middle' => null,
					'last' => config('seeder.admin' . $i . ".last"),
					'email' => config('seeder.admin' . $i . ".email"),
					'nick' => null,
					'dob' => "1969-06-09",
					'school_id' => ($i + 7),
				]);
			$admin->refresh();
			$admin->assignRole([SchoolRoles::$ADMIN, SchoolRoles::$EMPLOYEE, SchoolRoles::$STAFF]);
			if(config('seeder.admin' . $i . ".password", false) && $authService)
				if(($connection = $authService->connect($admin)))
				{
					$connection->setPassword(config('seeder.admin' . $i . ".password"));
					$admin->authConnection()
					      ->associate($connection);
					$admin->save();
				}
			$i++;
		}
		
	}
}
