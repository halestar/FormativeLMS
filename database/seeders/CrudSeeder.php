<?php

namespace Database\Seeders;

use App\Models\CRUD\Ethnicity;
use App\Models\CRUD\Gender;
use App\Models\CRUD\Honors;
use App\Models\CRUD\Pronouns;
use App\Models\CRUD\Relationship;
use App\Models\CRUD\Suffix;
use App\Models\CRUD\Title;
use App\Models\CRUD\ViewableGroup;
use App\Models\People\PersonalRelations;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

    }
}
