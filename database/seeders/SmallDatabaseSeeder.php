<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SmallDatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		$this->call(
			[
				PermissionSeeder::class,
				RoleSeeder::class,
				IntegrationSeeder::class,
				SystemSettingsSeeder::class,
				FieldPermissionsSeeder::class,
				SystemTableSeeder::class,
				SchoolEmailSeeder::class,
				BuildingSeeder::class,
				CampusSeeder::class,
				YearSeeder::class,
				AdminSeeder::class,
				SchoolIdSeeder::class,
				SchoolEmailSeeder::class,
				WebSeeder::class,
			]);
	}
}
