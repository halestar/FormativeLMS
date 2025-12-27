<?php

namespace Database\Seeders;

use App\Models\SubjectMatter\Learning\ClassCriteria;
use App\Models\SubjectMatter\SchoolClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = SchoolClass::all();
		foreach($classes as $class)
			ClassCriteria::factory()->count(4)->for($class)->create();
    }
}
