<?php

namespace Feature\Controllers\Courses;

use App\Models\People\Person;
use App\Models\SubjectMatter\Course;
use App\Models\SubjectMatter\Subject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CoursesCreatingTest extends TestCase
{
    use DatabaseTransactions;

    protected function store(int $subjectId): string
    {
        return route('subjects.courses.store', ['subject' => $subjectId]);
    }

    protected function data(): array
    {
        return [
            'name' => 'New Course',
            'subtitle' => 'New Subtitle',
            'code' => 'NC101',
            'credits' => 1.0,
        ];
    }

    public function test_store_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $subject = Subject::first();

        $response = $this->actingAs($admin)
            ->post($this->store($subject->id), $this->data());

        $course = Course::where('name', 'New Course')->first();
        $response->assertRedirect(route('subjects.courses.edit', $course));
        $this->assertDatabaseHas('courses', ['name' => 'New Course', 'subject_id' => $subject->id]);
    }

    public function test_store_staff(): void
    {
        $staff = Person::where('email', 'staff@kalinec.net')->first();
        $subject = Subject::first();

        $response = $this->actingAs($staff)
            ->post($this->store($subject->id), $this->data());

        $course = Course::where('name', 'New Course')->first();
        $response->assertRedirect(route('subjects.courses.edit', $course));
        $this->assertDatabaseHas('courses', ['name' => 'New Course', 'subject_id' => $subject->id]);
    }

    public function test_store_faculty(): void
    {
        $faculty = Person::where('email', 'faculty@kalinec.net')->first();
        $subject = Subject::first();

        $response = $this->actingAs($faculty)
            ->post($this->store($subject->id), $this->data());

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('courses', ['name' => 'New Course']);
    }

    public function test_store_student(): void
    {
        $student = Person::where('email', 'student@kalinec.net')->first();
        $subject = Subject::first();

        $response = $this->actingAs($student)
            ->post($this->store($subject->id), $this->data());

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('courses', ['name' => 'New Course']);
    }

    public function test_store_parent(): void
    {
        $parent = Person::where('email', 'parent@kalinec.net')->first();
        $subject = Subject::first();

        $response = $this->actingAs($parent)
            ->post($this->store($subject->id), $this->data());

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('courses', ['name' => 'New Course']);
    }
}
