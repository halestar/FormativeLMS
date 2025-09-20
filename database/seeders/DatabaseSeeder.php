<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
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
				CrudSeeder::class,
				SchoolEmailSeeder::class,
				BuildingSeeder::class,
				CampusSeeder::class,
				YearSeeder::class,
				RoomSeeder::class,
				AdminSeeder::class,
				FacultySeeder::class,
				StaffSeeder::class,
				CoachSeeder::class,
				FamilySeeder::class,
				SchoolIdSeeder::class,
				SubjectSeeder::class,
				CourseSeeder::class,
				PeriodSeeder::class,
				BlockSeeder::class,
				SchoolClassSeeder::class,
				ClassSessionSeeder::class,
				StudentEnrollmentSeeder::class,
				ClassMessageSeeder::class,
				StudentTrackerSeeder::class,
				SkillCategorySeeder::class,
				KnowledgeSkillSeeder::class,
				RubricRestoreSeeder::class,
				SchoolEmailSeeder::class,
				MimeSeeder::class,
			]);
	}
}
