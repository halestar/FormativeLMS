<?php

namespace Database\Seeders;

use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassMessage;
use Illuminate\Database\Seeder;

class ClassMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //we will generate messages for every student in every classroom. Yes, even K students.
        foreach(ClassSession::all() as $session)
        {
            //save the teacher to make teacher look up easier.
            $teacher = $session->teachers()->first();
            //also, this is only good if the class does have a teacher, if it doesn't skip it (it shouldn't happen)
            if(!$teacher)
                continue;
            $term = $session->term;
            foreach($session->students as $student)
            {
                //now we will create 1-5 random student messages
                ClassMessage::factory()
                    ->count(rand(5,10))
                    ->randomDateInTerm($term)
                    ->withSession($session)
                    ->withStudent($student)
                    ->withPostedBy($student->person)
                    ->fromStudent()
                    ->create();
                //and 5 more from the teacher
                ClassMessage::factory()
                    ->count(rand(5,10))
                    ->randomDateInTerm($term)
                    ->withSession($session)
                    ->withStudent($student)
                    ->withPostedBy($teacher)
                    ->fromTeacher()
                    ->create();

                //and 5 more from a parent
                ClassMessage::factory()
                    ->count(rand(5,10))
                    ->randomDateInTerm($term)
                    ->withSession($session)
                    ->withStudent($student)
                    ->withPostedBy($student->person->parents()->first())
                    ->fromParent()
                    ->create();
            }
        }
    }
}
