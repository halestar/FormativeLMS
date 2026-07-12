<?php

namespace Database\Seeders\CoreSeeders;

use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\IntegrationService;
use App\Models\Locations\Campus;
use App\Models\People\Person;
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
		if (config('seeder.admin_auth', false))
		{
			$authService = IntegrationService::select('integration_services.*')
			                                 ->join(
				                                 'integrators', 'integrators.id', '=',
				                                 'integration_services.integrator_id'
			                                 )
			                                 ->where('integrators.path', config('seeder.admin_auth'))
			                                 ->where(
				                                 'integration_services.service_type',
				                                 IntegratorServiceTypes::AUTHENTICATION
			                                 )
			                                 ->first();
		}
		$admin = Person::create(
			[
				'first'     => config('seeder.admin_first'),
				'middle'    => null,
				'last'      => config('seeder.admin_last'),
				'email'     => config('seeder.admin_email'),
				'nick'      => null,
				'dob'       => "1969-06-09",
				'school_id' => 1,
			]
		);
		$admin->refresh();
		$admin->portrait_url = config('app.url') . '/storage/idpics/1.jpg';
		$admin->save();
		$admin->assignRole([SchoolRoles::$ADMIN, SchoolRoles::$EMPLOYEE, SchoolRoles::$STAFF]);
		if (config('seeder.admin_password', false) && $authService)
		{
			if (($connection = $authService->connect($admin)))
			{
				$connection->setPassword(config('seeder.admin_password'));
				$admin->authConnection()
				      ->associate($connection);
				$admin->save();
			}

		}
		$admin->campuses()->sync(Campus::all()->pluck('id')->toArray());


		//finally, we add additional admins
		foreach (config('seeder.other_admins', []) as $otherAdmin)
		{
			$authService = IntegrationService::select('integration_services.*')
			                                 ->join(
				                                 'integrators', 'integrators.id', '=',
				                                 'integration_services.integrator_id'
			                                 )
			                                 ->where('integrators.path', $otherAdmin['auth'])
			                                 ->where(
				                                 'integration_services.service_type',
				                                 IntegratorServiceTypes::AUTHENTICATION
			                                 )
			                                 ->first();
			$admin = Person::create(
				[
					'first'     => $otherAdmin['first'],
					'last'      => $otherAdmin['last'],
					'email'     => $otherAdmin['email'],
					'dob'       => "1969-06-09",
					'school_id' => uniqid(),
				]
			);
			$admin->refresh();
			$admin->assignRole([SchoolRoles::$ADMIN, SchoolRoles::$EMPLOYEE, SchoolRoles::$STAFF]);
			if ($otherAdmin['password'] && $authService)
			{
				if (($connection = $authService->connect($admin)))
				{
					$connection->setPassword($otherAdmin['password']);
					$admin->authConnection()
					      ->associate($connection);
					$admin->save();
				}
			}
		}

	}
}
