<?php

namespace Database\Seeders;

use App\Classes\Integrators\IntegrationsManager;
use App\Classes\Integrators\Local\LocalIntegrator;
use App\Models\Integrations\Integrator;
use App\Models\Utilities\SchoolRoles;
use halestar\FabLmsGoogleIntegrator\GoogleIntegrator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class IntegrationSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		//register the integrations here.
		$manager = App::make(IntegrationsManager::class);
		$manager->registerIntegrator(LocalIntegrator::class, true);
		$manager->registerIntegrator(GoogleIntegrator::class, false);
		//Assign basic permissions
		foreach(Integrator::all() as $integrator)
			$integrator->assignRole([SchoolRoles::$EMPLOYEE, SchoolRoles::$STUDENT, SchoolRoles::$PARENT]);
	}
}
