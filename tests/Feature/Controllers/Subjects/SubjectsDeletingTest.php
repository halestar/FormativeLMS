<?php

namespace Feature\Controllers\Subjects;

use App\Models\People\Person;
use App\Models\SubjectMatter\Subject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SubjectsDeletingTest extends TestCase
{
    use DatabaseTransactions;

    protected function destroy(Subject $subject): string
    {
        return route('subjects.subjects.destroy', ['subject' => $subject->id]);
    }

    public function test_delete_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $subject = Subject::first();
        $response = $this->actingAs($admin)
            ->delete($this->destroy($subject));

        $response->assertRedirect(route('subjects.subjects.index', ['campus' => $subject->campus_id]));
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    public function test_delete_staff(): void
    {
        $staff = Person::where('email', 'staff@kalinec.net')->first();
        $subject = Subject::first();
        $response = $this->actingAs($staff)
            ->delete($this->destroy($subject));

        $response->assertRedirect(route('subjects.subjects.index', ['campus' => $subject->campus_id]));
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    public function test_delete_faculty(): void
    {
        $faculty = Person::where('email', 'faculty@kalinec.net')->first();
        $subject = Subject::first();
        $response = $this->actingAs($faculty)
            ->delete($this->destroy($subject));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('subjects', ['id' => $subject->id]);
    }

    public function test_delete_student(): void
    {
        $student = Person::where('email', 'student@kalinec.net')->first();
        $subject = Subject::first();
        $response = $this->actingAs($student)
            ->delete($this->destroy($subject));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('subjects', ['id' => $subject->id]);
    }

    public function test_delete_parent(): void
    {
        $parent = Person::where('email', 'parent@kalinec.net')->first();
        $subject = Subject::first();
        $response = $this->actingAs($parent)
            ->delete($this->destroy($subject));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('subjects', ['id' => $subject->id]);
    }
}
