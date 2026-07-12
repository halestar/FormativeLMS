<?php

namespace Database\Seeders;

use Database\Seeders\CoreSeeders\AdminSeeder;
use Database\Seeders\CoreSeeders\IntegrationSeeder;
use Database\Seeders\CoreSeeders\LearningDemonstrationTypeSeeder;
use Database\Seeders\CoreSeeders\MimeSeeder;
use Database\Seeders\CoreSeeders\NotificationsSeeder;
use Database\Seeders\CoreSeeders\PermissionSeeder;
use Database\Seeders\CoreSeeders\RoleSeeder;
use Database\Seeders\CoreSeeders\YearSeeder;
use Illuminate\Database\Seeder;

class EmptySeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$this->call
		(
			[
				PermissionSeeder::class,
				RoleSeeder::class,
				IntegrationSeeder::class,
				LearningDemonstrationTypeSeeder::class,
				YearSeeder::class,
				AdminSeeder::class,
				MimeSeeder::class,
				NotificationsSeeder::class,
			]
		);
	}
}
