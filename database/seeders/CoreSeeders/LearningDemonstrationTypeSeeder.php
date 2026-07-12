<?php

namespace Database\Seeders\CoreSeeders;

use App\Models\SubjectMatter\Learning\LearningDemonstrationType;
use Illuminate\Database\Seeder;

class LearningDemonstrationTypeSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		LearningDemonstrationType::create(
			[
				'name' => 'Standard',
			]
		);
	}
}
