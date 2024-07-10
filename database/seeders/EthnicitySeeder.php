<?php

namespace Database\Seeders;

use App\Models\CRUD\Ethnicity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EthnicitySeeder extends Seeder
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
    }
}
