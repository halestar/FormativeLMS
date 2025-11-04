<?php

namespace Database\Seeders;

use App\Models\SystemTables\DismissalReason;
use App\Models\SystemTables\Level;
use App\Models\SystemTables\Relationship;
use App\Models\SystemTables\SchoolArea;
use App\Models\SystemTables\SkillCategoryDesignation;
use Illuminate\Database\Seeder;

class SystemTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		
		Relationship::insert(
			[
				['id' => Relationship::PARENT, 'name' => "Parent", 'className' => Relationship::class],
				['id' => Relationship::STEPPARENT, 'name' => "Step Parent", 'className' => Relationship::class],
				['id' => Relationship::GUARDIAN, 'name' => "Guardian", 'className' => Relationship::class],
				['id' => Relationship::CHILD, 'name' => "Child", 'className' => Relationship::class],
				['id' => Relationship::SPOUSE, 'name' => "Spouse", 'className' => Relationship::class],
				['id' => Relationship::GRANDPARENT, 'name' => "Grandparent", 'className' => Relationship::class],
			]);
		
		Level::insert(
			[
				['name' => '12th', 'order' => 13, 'className' => Level::class],
				['name' => '11th', 'order' => 12, 'className' => Level::class],
				['name' => '10th', 'order' => 11, 'className' => Level::class],
				['name' => '9th', 'order' => 10, 'className' => Level::class],
				['name' => '8th', 'order' => 9, 'className' => Level::class],
				['name' => '7th', 'order' => 8, 'className' => Level::class],
				['name' => '6th', 'order' => 7, 'className' => Level::class],
				['name' => '5th', 'order' => 6, 'className' => Level::class],
				['name' => '4th', 'order' => 5, 'className' => Level::class],
				['name' => '3rd', 'order' => 4, 'className' => Level::class],
				['name' => '2nd', 'order' => 3, 'className' => Level::class],
				['name' => '1st', 'order' => 2, 'className' => Level::class],
				['name' => 'Kindergarten', 'order' => 1, 'className' => Level::class],
			]);
		
		SchoolArea::insert(
			[
				['name' => '1st Floor', 'className' => SchoolArea::class],
				['name' => '2nd Floor', 'className' => SchoolArea::class],
				['name' => '3rd Floor', 'className' => SchoolArea::class],
				['name' => '4th Floor', 'className' => SchoolArea::class],
				['name' => '5th Floor', 'className' => SchoolArea::class],
				['name' => 'School Yard', 'className' => SchoolArea::class],
				['name' => 'Theater', 'className' => SchoolArea::class],
				['name' => 'Garden', 'className' => SchoolArea::class],
				['name' => 'Off-Campus', 'className' => SchoolArea::class],
			]);
		
		DismissalReason::insert(
			[
				['name' => 'Left', 'className' => DismissalReason::class],
				['name' => 'Expelled', 'className' => DismissalReason::class],
				['name' => 'Attrition', 'className' => DismissalReason::class],
			]);
	}
}
