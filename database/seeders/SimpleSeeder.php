<?php

namespace Database\Seeders;

use Database\Seeders\CoreSeeders\AdminSeeder;
use Database\Seeders\CoreSeeders\FieldPermissionsSeeder;
use Database\Seeders\CoreSeeders\IntegrationSeeder;
use Database\Seeders\CoreSeeders\LearningDemonstrationTypeSeeder;
use Database\Seeders\CoreSeeders\MimeSeeder;
use Database\Seeders\CoreSeeders\NotificationsSeeder;
use Database\Seeders\CoreSeeders\PermissionSeeder;
use Database\Seeders\CoreSeeders\RoleSeeder;
use Database\Seeders\SimpleSeeder\BlockSeeder;
use Database\Seeders\SimpleSeeder\BuildingSeeder;
use Database\Seeders\SimpleSeeder\CampusSeeder;
use Database\Seeders\SimpleSeeder\ClassCriteriaSeeder;
use Database\Seeders\SimpleSeeder\ClassMessageSeeder;
use Database\Seeders\SimpleSeeder\ClassSessionSeeder;
use Database\Seeders\SimpleSeeder\CoachSeeder;
use Database\Seeders\SimpleSeeder\CourseSeeder;
use Database\Seeders\SimpleSeeder\DemonstrationSeeder;
use Database\Seeders\SimpleSeeder\FacultySeeder;
use Database\Seeders\SimpleSeeder\FamilySeeder;
use Database\Seeders\SimpleSeeder\GradingTranslationSchemaSeeder;
use Database\Seeders\SimpleSeeder\PeriodSeeder;
use Database\Seeders\SimpleSeeder\RoleFieldsSeeder;
use Database\Seeders\SimpleSeeder\RoomSeeder;
use Database\Seeders\SimpleSeeder\RubricRestoreSeeder;
use Database\Seeders\SimpleSeeder\SchoolClassSeeder;
use Database\Seeders\SimpleSeeder\SchoolIdSeeder;
use Database\Seeders\SimpleSeeder\ServiceAccountsSeeder;
use Database\Seeders\SimpleSeeder\SkillCategorySeeder;
use Database\Seeders\SimpleSeeder\SkillSeeder;
use Database\Seeders\SimpleSeeder\StaffSeeder;
use Database\Seeders\SimpleSeeder\StudentEnrollmentSeeder;
use Database\Seeders\SimpleSeeder\StudentTrackerSeeder;
use Database\Seeders\SimpleSeeder\SubjectSeeder;
use Database\Seeders\SimpleSeeder\SystemSettingsSeeder;
use Database\Seeders\SimpleSeeder\SystemTableSeeder;
use Database\Seeders\SimpleSeeder\YearSeeder;
use Illuminate\Database\Seeder;

class SimpleSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$this->call(
			[
				PermissionSeeder::class,
				RoleSeeder::class,
				RoleFieldsSeeder::class,
				IntegrationSeeder::class,
				SystemSettingsSeeder::class,
				FieldPermissionsSeeder::class,
				SystemTableSeeder::class,
				LearningDemonstrationTypeSeeder::class,
				BuildingSeeder::class,
				CampusSeeder::class,
				YearSeeder::class,
				RoomSeeder::class,
				GradingTranslationSchemaSeeder::class,
				AdminSeeder::class,
				ServiceAccountsSeeder::class,
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
				SkillSeeder::class,
				RubricRestoreSeeder::class,
				MimeSeeder::class,
				NotificationsSeeder::class,
				ClassCriteriaSeeder::class,
				DemonstrationSeeder::class,
			]
		);
	}
}
