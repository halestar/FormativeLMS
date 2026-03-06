<?php

namespace Feature\Controllers\Subjects;

use App\Models\People\Person;
use App\Models\SubjectMatter\Subject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SubjectsEditingTest extends TestCase
{
    use DatabaseTransactions;

    protected function edit(Subject $subject): string
    {
        return route('subjects.subjects.edit', ['subject' => $subject->id]);
    }

    protected function update(Subject $subject): string
    {
        return route('subjects.subjects.update', ['subject' => $subject->id]);
    }

    protected function data(Subject $subject): array
    {
        return
        [
            'name' => $subject->name.' Updated',
            'color' => '#00ff00',
            'required_terms' => 2,
        ];
    }

    public function test_edit_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $subject = Subject::first();
        $response = $this->actingAs($admin)
            ->get($this->edit($subject));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee($subject->name);
    }

    public function test_edit_staff(): void
    {
        $staff = Person::where('email', 'staff@kalinec.net')->first();
        $subject = Subject::first();
        $response = $this->actingAs($staff)
            ->get($this->edit($subject));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee($subject->name);
    }

    public function test_edit_faculty(): void
    {
        $faculty = Person::where('email', 'faculty@kalinec.net')->first();
        $subject = Subject::first();
        $response = $this->actingAs($faculty)
            ->get($this->edit($subject));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_edit_student(): void
    {
        $student = Person::where('email', 'student@kalinec.net')->first();
        $subject = Subject::first();
        $response = $this->actingAs($student)
            ->get($this->edit($subject));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_edit_parent(): void
    {
        $parent = Person::where('email', 'parent@kalinec.net')->first();
        $subject = Subject::first();
        $response = $this->actingAs($parent)
            ->get($this->edit($subject));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_update_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $subject = Subject::first();
        $data = $this->data($subject);
        $response = $this->actingAs($admin)
            ->put($this->update($subject), $data);

        $response->assertRedirect(route('subjects.subjects.index', ['campus' => $subject->campus_id]));
        $this->assertDatabaseHas('subjects', ['id' => $subject->id, 'name' => $data['name']]);
    }

    public function test_update_staff(): void
    {
        $staff = Person::where('email', 'staff@kalinec.net')->first();
        $subject = Subject::first();
        $data = $this->data($subject);
        $response = $this->actingAs($staff)
            ->put($this->update($subject), $data);

        $response->assertRedirect(route('subjects.subjects.index', ['campus' => $subject->campus_id]));
        $this->assertDatabaseHas('subjects', ['id' => $subject->id, 'name' => $data['name']]);
    }

    public function test_update_faculty(): void
    {
        $faculty = Person::where('email', 'faculty@kalinec.net')->first();
        $subject = Subject::first();
        $data = $this->data($subject);
        $response = $this->actingAs($faculty)
            ->put($this->update($subject), $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id, 'name' => $data['name']]);
    }

    public function test_update_student(): void
    {
        $student = Person::where('email', 'student@kalinec.net')->first();
        $subject = Subject::first();
        $data = $this->data($subject);
        $response = $this->actingAs($student)
            ->put($this->update($subject), $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id, 'name' => $data['name']]);
    }

    public function test_update_parent(): void
    {
        $parent = Person::where('email', 'parent@kalinec.net')->first();
        $subject = Subject::first();
        $data = $this->data($subject);
        $response = $this->actingAs($parent)
            ->put($this->update($subject), $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id, 'name' => $data['name']]);
    }

    public function test_update_validation_failures(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $subject = Subject::first();

        // Empty name
        $this->actingAs($admin)
            ->put($this->update($subject), ['name' => '', 'color' => '#000000'])
            ->assertSessionHasErrors(['name']);

        // Invalid required_terms
        $this->actingAs($admin)
            ->put($this->update($subject), ['name' => 'Name', 'color' => '#000000', 'required_terms' => -1])
            ->assertSessionHasErrors(['required_terms']);
    }

    public function test_update_active_toggle(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $subject = Subject::first();
        $subject->update(['active' => true]);

        // Toggle to inactive
        $this->actingAs($admin)
            ->put($this->update($subject), [
                'name' => $subject->name,
                'color' => $subject->color,
                'active' => 0,
            ])
            ->assertRedirect();

        $this->assertFalse($subject->fresh()->active);

        // Toggle back to active
        $this->actingAs($admin)
            ->put($this->update($subject), [
                'name' => $subject->name,
                'color' => $subject->color,
                'active' => 1,
            ])
            ->assertRedirect();

        $this->assertTrue($subject->fresh()->active);
    }
}
