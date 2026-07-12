<?php

namespace Database\Seeders\CoreSeeders;

use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
	private array $baseRolePermissions =
		[
			'DB Editor' => [
				'people.view',
				'people.edit',
				'people.create',
				'people.delete',
				'people.merge',
				'people.assign.roles',
				'people.search',
				'people.field.permissions',
				'people.assign.roles',
				'people.roles.fields',
				'people.ids.manage',
			],
			'School Manager' => [
				'people.ids.manage',
				'school.tracker.admin',
				'settings.integrators',
				'system.ai',
				'system.tables',
				'school',
			],
			'Impersonator' => [
				'people.impersonate',
			],
			'Permission Editor' => [
				'settings.permissions.view',
				'settings.permissions.edit',
				'settings.permissions.create',
				'settings.permissions.delete',
				'settings.roles.view',
				'settings.roles.update',
				'settings.roles.create',
				'settings.roles.delete',
				'people.password',
			],
			'Person Contact Editor' => [],
			'Academic Manager' => [
				'subjects.subjects',
				'subjects.courses',
				'subjects.classes',
				'classes.enrollment',
				'school.tracker.admin',
				'subjects.classes.view',
				'subjects.classes.manage',
				'school.tracker.admin',
			],
			'Locations Manager' => [
				'locations.campuses',
				'locations.years',
				'locations.terms',
				'locations.buildings',
				'locations.areas',
				'locations.rooms',
			],
			'Schedule Manager' => [
				'locations.periods',
				'locations.blocks',
				'classes.enrollment',
				'subjects.classes.view',
			],
			'Student Tracker' => [
				'school.tracker',
			],
			'Skills Administrator' => [
				'subjects.skills',
			],
			'Substitute Manager' => [
				'substitute.admin',
			],
		];


	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		foreach (SchoolRoles::$baseRolePermissions as $roleName => $permissions)
		{
			SchoolRoles::create(['name' => $roleName, 'base_role' => true])
			           ->syncPermissions($permissions);
		}

		foreach ($this->baseRolePermissions as $roleName => $permissions)
		{
			SchoolRoles::create(['name' => $roleName])
			           ->syncPermissions($permissions);
		}

	}
}
