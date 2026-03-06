<?php

namespace Feature\Controllers\Courses;

use App\Models\People\Person;
use App\Models\SubjectMatter\Course;
use App\Models\SubjectMatter\Subject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CoursesDeletingTest extends TestCase
{
    use DatabaseTransactions;

    protected function index(int $subjectId): string
    {
        return route('subjects.courses.index', ['subject' => $subjectId]);
    }

    protected function deleteRoute(int $id): string
    {
        return route('subjects.courses.destroy', ['course' => $id]);
    }

    public function test_delete_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $subject = Subject::first();
        $course = new Course;
        $course->name = 'Temp Course';
        $course->subject_id = $subject->id;
        $course->credits = 1;
        $course->save();

        $response = $this->actingAs($admin)
            ->delete($this->deleteRoute($course->id));

        $response->assertRedirect($this->index($subject->id));
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_delete_staff(): void
    {
        $staff = Person::where('email', 'staff@kalinec.net')->first();
        $subject = Subject::first();
        $course = new Course;
        $course->name = 'Temp Course';
        $course->subject_id = $subject->id;
        $course->credits = 1;
        $course->save();

        $response = $this->actingAs($staff)
            ->delete($this->deleteRoute($course->id));

        $response->assertRedirect($this->index($subject->id));
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_delete_faculty(): void
    {
        $faculty = Person::where('email', 'faculty@kalinec.net')->first();
        $subject = Subject::first();
        $course = new Course;
        $course->name = 'Temp Course';
        $course->subject_id = $subject->id;
        $course->credits = 1;
        $course->save();

        $response = $this->actingAs($faculty)
            ->delete($this->deleteRoute($course->id));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }

    public function test_delete_student(): void
    {
        $student = Person::where('email', 'student@kalinec.net')->first();
        $subject = Subject::first();
        $course = new Course;
        $course->name = 'Temp Course';
        $course->subject_id = $subject->id;
        $course->credits = 1;
        $course->save();

        $response = $this->actingAs($student)
            ->delete($this->deleteRoute($course->id));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }

    public function test_delete_parent(): void
    {
        $parent = Person::where('email', 'parent@kalinec.net')->first();
        $subject = Subject::first();
        $course = new Course;
        $course->name = 'Temp Course';
        $course->subject_id = $subject->id;
        $course->credits = 1;
        $course->save();

        $response = $this->actingAs($parent)
            ->delete($this->deleteRoute($course->id));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }
}
