<?php

namespace Database\Seeders;

use Database\Seeders\CoreSeeders\IntegrationSeeder;
use Database\Seeders\CoreSeeders\PermissionSeeder;
use Database\Seeders\CoreSeeders\RoleSeeder;
use Database\Seeders\SimpleSeeder\RoleFieldsSeeder;
use Illuminate\Database\Seeder;

/**
 * This seeder is able to seed a database for a sample elementary school. All personal data is faked and
 * is not gotten from any other sources. The only exception to this is the skills and rubric sections, which
 * are generated from the Los Angeles Unified School District as their public school standards.
 *
 * This seeder will create an elementary school with the following characteristics:
 * - Name: FabLMS Elementary School
 * - Abbreviation: ES
 * - Levels: K-5
 *   - Each level will have 20 students
 *   - Each level will be taught by a primary teacher and an assistant teacher
 * - Curriculum
 *   - Each level will have 4 core subjects
 *     - Language Arts
 *     - Math
 *     - Science
 *     - Social Studies
 *   - Each core subject will be taught by the primary teacher
 *   - Each Level will have 3 specialist subjects
 *     - Art
 *     - Music
 *     - Physical Education
 *   - Each specialist subject will be taught by two specialist teachers with rotating schedules
 * - Special Users
 *   - Default special users
 */
class ElementarySchoolSeeder extends Seeder
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

			]);
	}
}
