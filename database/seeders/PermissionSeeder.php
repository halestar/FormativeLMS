<?php

namespace Database\Seeders;

use App\Models\Utilities\PermissionCategory;
use App\Models\Utilities\SchoolPermission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
	private static array $categories =
		[
			'Roles/Permissions',
			'Personal Information',
			'Locations',
			'Subject Matter',
			'Class Management',
			'System',
			'School Administration'
		];
	
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		foreach(static::$categories as $category)
		{
			$cat = PermissionCategory::create(['name' => $category]);
			foreach((static::permissions())[$category] as $permission)
				SchoolPermission::create($permission + ['category_id' => $cat->id]);
		}
	}
	
	private static function permissions(): array
	{
		return [
			'Roles/Permissions' =>
				[
					['name' => 'settings.permissions.view', 'description' => 'View permissions in the system'],
					['name' => 'settings.permissions.edit', 'description' => 'Edit permissions in the system'],
					['name' => 'settings.permissions.create', 'description' => 'Create permissions in the system'],
					['name' => 'settings.permissions.delete', 'description' => 'Delete permissions in the system'],
					['name' => 'settings.roles.view', 'description' => 'View roles in the system'],
					['name' => 'settings.roles.update', 'description' => 'Edit roles in the system'],
					['name' => 'settings.roles.create', 'description' => 'Create roles in the system'],
					['name' => 'settings.roles.delete', 'description' => 'Delete roles in the system'],
				],
			'Personal Information' =>
				[
					['name' => 'people.view',
					 'description' => 'This permission allows full view into a person\'s profile'],
					['name' => 'people.edit',
					 'description' => 'This permission allows full edit of a person\'s information'],
					['name' => 'people.create', 'description' => 'Create a new Person'],
					['name' => 'people.delete', 'description' => 'Soft Delete a person'],
					['name' => 'people.merge', 'description' => 'Merge two person records into one.'],
					['name' => 'people.search', 'description' => 'Search for people in the system'],
					['name' => 'people.field.permissions',
					 'description' => 'Ability to define which fields are viewable by which roles.'],
					['name' => 'people.assign.roles', 'description' => 'Ability to assign roles to users.'],
					['name' => 'people.roles.fields', 'description' => 'Ability to define special fields for roles'],
					['name' => 'people.ids.manage', 'description' => 'Ability to manage IDs for people in the system'],
					['name' => 'people.password', 'description' => 'Ability to change user\'s passwords'],
					['name' => 'people.impersonate', 'description' => 'Ability to impersonate other users'],
				],
			'Locations' =>
				[
					['name' => 'locations.campuses', 'description' => 'Ability to edit campuses in the system'],
					['name' => 'locations.years', 'description' => 'Ability to edit years in the system'],
					['name' => 'locations.terms', 'description' => 'Ability to edit terms in the system'],
					['name' => 'locations.buildings', 'description' => 'Ability to buildings and rooms in the system'],
					['name' => 'locations.areas', 'description' => 'Ability to edit areas in the system'],
					['name' => 'locations.rooms', 'description' => 'Ability to edit rooms in the system'],
					['name' => 'locations.periods',
					 'description' => 'Ability to edit campuses\' periods in the system'],
					['name' => 'locations.blocks', 'description' => 'Ability to edit campuses\' blocks in the system'],
				],
			'Subject Matter' =>
				[
					['name' => 'subjects.subjects', 'description' => 'Ability to edit Subjects in the system'],
					['name' => 'subjects.courses', 'description' => 'Ability to edit Courses in the system'],
					['name' => 'subjects.classes', 'description' => 'Ability to edit year-long classes in the system'],
					['name' => 'subjects.classes.view', 'description' => 'Ability to view details about a class'],
					['name' => 'subjects.classes.manage',
					 'description' => 'Global permission to manage any class in the system'],
					['name' => 'subjects.skills', 'description' => 'Ability to manage skills in the system'],
				],
			'Class Management' =>
				[
					['name' => 'classes.enrollment', 'description' => 'Ability to enroll students in classes'],
				],
			'School Administration' =>
				[
					['name' => 'school.tracker', 'description' => "Ability to be a tracker for a student"],
					['name' => 'school.tracker.admin', 'description' => "Ability to assign trackers to students"],
				],
			'System' =>
				[
					['name' => 'cms', 'description' => __('permissions.cms')],
					['name' => 'crud', 'description' => __('permissions.crud')],
					['name' => 'school', 'description' => __('permissions.school')],
					['name' => 'school.emails', 'description' => __('permissions.school.emails')],
					['name' => 'settings.integrators', 'description' => __('permissions.settings.integrators')],
					['name' => 'system.ai', 'description' => __('permissions.system.ai')],
				],
		];
	}
}
