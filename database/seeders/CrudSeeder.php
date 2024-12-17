<?php

namespace Database\Seeders;

use App\Models\CRUD\DismissalReason;
use App\Models\CRUD\Ethnicity;
use App\Models\CRUD\Gender;
use App\Models\CRUD\Honors;
use App\Models\CRUD\Level;
use App\Models\CRUD\Pronouns;
use App\Models\CRUD\Relationship;
use App\Models\CRUD\SchoolArea;
use App\Models\CRUD\Suffix;
use App\Models\CRUD\Title;
use App\Models\CRUD\ViewableGroup;
use Illuminate\Database\Seeder;

class CrudSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ethnicity::insert(
            [
                ['name' => "Hispanic or Latino"],
                ['name' => "American Indian or Alaska Native"],
                ['name' => "Asian"],
                ['name' => "Black or African American"],
                ['name' => "Native Hawaiian or Other Pacific Islander"],
                ['name' => "White"],
            ]);

        Title::insert(
            [
                ['name' => "Mr."],
                ['name' => "Ms."],
                ['name' => "Mrs."],
                ['name' => "Mx."],
            ]);

        Suffix::insert(
            [
                ['name' => "Jr."],
                ['name' => "II"],
                ['name' => "III"],
                ['name' => "IV"],
            ]);

        Honors::insert(
            [
                ['name' => "MD"],
                ['name' => "PhD"],
            ]);

        Gender::insert(
            [
                ['name' => "Male"],
                ['name' => "Female"],
                ['name' => "Non-Binary"],
            ]);

        Pronouns::insert(
            [
                ['name' => "He/Him"],
                ['name' => "She/Her"],
                ['name' => "They/Them"],
            ]);

        ViewableGroup::insert(
            [
                ['id' => ViewableGroup::HIDDEN, 'name' => "Other Information"],
                ['id' => ViewableGroup::BASIC_INFO, 'name' => "Basic Information"],
                ['id' => ViewableGroup::CONTACT_INFO, 'name' => "Contact Information"],
                ['id' => ViewableGroup::RELATIONSHIPS, 'name' => "Relationships"],
            ]);

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
                ['name' => '12th Grade', 'order' => 13],
                ['name' => '11th Grade', 'order' => 12],
                ['name' => '10th Grade', 'order' => 11],
                ['name' => '9th Grade', 'order' => 10],
                ['name' => '8th Grade', 'order' => 9],
                ['name' => '7th Grade', 'order' => 8],
                ['name' => '6th Grade', 'order' => 7],
                ['name' => '5th Grade', 'order' => 6],
                ['name' => '4th Grade', 'order' => 5],
                ['name' => '3rd Grade', 'order' => 4],
                ['name' => '2nd Grade', 'order' => 3],
                ['name' => '1st Grade', 'order' => 2],
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
