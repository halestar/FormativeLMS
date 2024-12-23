<?php

namespace Database\Seeders;

use App\Models\Locations\Year;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\SchoolClass;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Seeder;

class ClassSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = Year::currentYear();
        foreach (SchoolClass::all() as $schoolClass)
        {
            $room = $schoolClass->course->campus->rooms()->inRandomOrder()->first();
            $block = $schoolClass->course->campus->blocks()->inRandomOrder()->first();
            $teacher = $schoolClass->course->campus->employeesByRole(SchoolRoles::$FACULTY)->random();
            foreach($year->campusTerms($schoolClass->course->campus)->get() as $term)
            {
                $session = ClassSession::create(
                    [
                        'class_id' => $schoolClass->id,
                        'term_id' => $term->id,
                        'room_id' => $room->id,
                        'block_id' => $block->id,
                    ]);
                $session->teachers()->attach($teacher);
            }
        }
    }
}
