<?php

namespace Feature\Controllers\Courses;

use App\Models\People\Person;
use App\Models\SubjectMatter\Course;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CoursesEditingTest extends TestCase
{
    use DatabaseTransactions;

    protected function edit(int $id): string
    {
        return route('subjects.courses.edit', ['course' => $id]);
    }

    protected function update(int $id): string
    {
        return route('subjects.courses.update', ['course' => $id]);
    }

    protected function data(Course $course): array
    {
        return [
            'name' => $course->name.'_updated',
            'subtitle' => 'Updated Subtitle',
            'code' => 'UP101',
            'description' => 'Updated Description',
            'credits' => 2.0,
            'on_transcript' => 1,
            'gb_required' => 1,
            'honors' => 1,
            'ap' => 1,
            'can_assign_honors' => 1,
            'active' => 1,
        ];
    }

    public function test_edit_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $course = Course::first();

        $response = $this->actingAs($admin)
            ->get($this->edit($course->id));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee($course->name);
    }

    public function test_edit_staff(): void
    {
        $staff = Person::where('email', 'staff@kalinec.net')->first();
        $course = Course::first();

        $response = $this->actingAs($staff)
            ->get($this->edit($course->id));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee($course->name);
    }

    public function test_edit_faculty(): void
    {
        $faculty = Person::where('email', 'faculty@kalinec.net')->first();
        $course = Course::first();

        $response = $this->actingAs($faculty)
            ->get($this->edit($course->id));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_edit_student(): void
    {
        $student = Person::where('email', 'student@kalinec.net')->first();
        $course = Course::first();

        $response = $this->actingAs($student)
            ->get($this->edit($course->id));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_edit_parent(): void
    {
        $parent = Person::where('email', 'parent@kalinec.net')->first();
        $course = Course::first();

        $response = $this->actingAs($parent)
            ->get($this->edit($course->id));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_update_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $course = Course::first();
        $data = $this->data($course);

        $response = $this->actingAs($admin)
            ->put($this->update($course->id), $data);

        $response->assertRedirect(route('subjects.courses.index', ['subject' => $course->subject_id]));
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'name' => $data['name']]);
    }

    public function test_update_staff(): void
    {
        $staff = Person::where('email', 'staff@kalinec.net')->first();
        $course = Course::first();
        $data = $this->data($course);

        $response = $this->actingAs($staff)
            ->put($this->update($course->id), $data);

        $response->assertRedirect(route('subjects.courses.index', ['subject' => $course->subject_id]));
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'name' => $data['name']]);
    }

    public function test_update_faculty(): void
    {
        $faculty = Person::where('email', 'faculty@kalinec.net')->first();
        $course = Course::first();
        $data = $this->data($course);

        $response = $this->actingAs($faculty)
            ->put($this->update($course->id), $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('courses', ['id' => $course->id, 'name' => $data['name']]);
    }

    public function test_update_student(): void
    {
        $student = Person::where('email', 'student@kalinec.net')->first();
        $course = Course::first();
        $data = $this->data($course);

        $response = $this->actingAs($student)
            ->put($this->update($course->id), $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('courses', ['id' => $course->id, 'name' => $data['name']]);
    }

    public function test_update_parent(): void
    {
        $parent = Person::where('email', 'parent@kalinec.net')->first();
        $course = Course::first();
        $data = $this->data($course);

        $response = $this->actingAs($parent)
            ->put($this->update($course->id), $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('courses', ['id' => $course->id, 'name' => $data['name']]);
    }
}
