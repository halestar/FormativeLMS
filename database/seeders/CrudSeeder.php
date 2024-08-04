<?php

namespace Database\Seeders;

use App\Models\CRUD\Ethnicity;
use App\Models\CRUD\Gender;
use App\Models\CRUD\Honors;
use App\Models\CRUD\Pronouns;
use App\Models\CRUD\Suffix;
use App\Models\CRUD\Title;
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

    }
}
