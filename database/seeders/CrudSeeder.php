<?php

namespace Database\Seeders;

use App\Models\CRUD\DismissalReason;
use App\Models\CRUD\Level;
use App\Models\CRUD\Relationship;
use App\Models\CRUD\SchoolArea;
use Illuminate\Database\Seeder;

class CrudSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Relationship::insert(
            [
                ['id' => Relationship::PARENT, 'name' => "Parent"],
                ['id' => Relationship::STEPPARENT, 'name' => "Step Parent"],
                ['id' => Relationship::GUARDIAN, 'name' => "Guardian"],
                ['id' => Relationship::CHILD, 'name' => "Child"],
                ['id' => Relationship::SPOUSE, 'name' => "Spouse"],
                ['id' => Relationship::GRANDPARENT, 'name' => "Grandparent"],
            ]);

        Level::insert(
            [
                ['name' => '12th', 'order' => 13],
                ['name' => '11th', 'order' => 12],
                ['name' => '10th', 'order' => 11],
                ['name' => '9th', 'order' => 10],
                ['name' => '8th', 'order' => 9],
                ['name' => '7th', 'order' => 8],
                ['name' => '6th', 'order' => 7],
                ['name' => '5th', 'order' => 6],
                ['name' => '4th', 'order' => 5],
                ['name' => '3rd', 'order' => 4],
                ['name' => '2nd', 'order' => 3],
                ['name' => '1st', 'order' => 2],
                ['name' => 'Kindergarten', 'order' => 1],
            ]);

        SchoolArea::insert(
            [
                ['name' => '1st Floor'],
                ['name' => '2nd Floor'],
                ['name' => '3rd Floor'],
                ['name' => '4th Floor'],
                ['name' => '5th Floor'],
                ['name' => 'School Yard'],
                ['name' => 'Theater'],
                ['name' => 'Garden'],
                ['name' => 'Off-Campus'],
            ]);

        DismissalReason::insert(
            [
                ['name' => 'Left'],
                ['name' => 'Expelled'],
                ['name' => 'Attrition'],
            ]);
    }
}
