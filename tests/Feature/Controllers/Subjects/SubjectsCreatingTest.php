<?php

namespace Feature\Controllers\Subjects;

use App\Models\Locations\Campus;
use App\Models\People\Person;
use App\Models\SubjectMatter\Subject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SubjectsCreatingTest extends TestCase
{
    use DatabaseTransactions;

    protected function store(Campus $campus): string
    {
        return route('subjects.subjects.store', ['campus' => $campus->id]);
    }

    protected function data(): array
    {
        return
        [
            'name' => 'New Subject',
            'color' => '#ff0000',
        ];
    }

    public function test_store_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $campus = Campus::first();
        $response = $this->actingAs($admin)
            ->post($this->store($campus), $this->data());

        $subject = Subject::where('name', $this->data()['name'])->first();
        $response->assertRedirect(route('subjects.subjects.edit', ['subject' => $subject->id]));
        $this->assertDatabaseHas('subjects', ['name' => $this->data()['name']]);
    }

    public function test_store_staff(): void
    {
        $staff = Person::where('email', 'staff@kalinec.net')->first();
        $campus = Campus::first();
        $response = $this->actingAs($staff)
            ->post($this->store($campus), $this->data());

        $subject = Subject::where('name', $this->data()['name'])->first();
        $response->assertRedirect(route('subjects.subjects.edit', ['subject' => $subject->id]));
        $this->assertDatabaseHas('subjects', ['name' => $this->data()['name']]);
    }

    public function test_store_faculty(): void
    {
        $faculty = Person::where('email', 'faculty@kalinec.net')->first();
        $campus = Campus::first();
        $response = $this->actingAs($faculty)
            ->post($this->store($campus), $this->data());
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('subjects', ['name' => $this->data()['name']]);
    }

    public function test_store_student(): void
    {
        $student = Person::where('email', 'student@kalinec.net')->first();
        $campus = Campus::first();
        $response = $this->actingAs($student)
            ->post($this->store($campus), $this->data());
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('subjects', ['name' => $this->data()['name']]);
    }

    public function test_store_parent(): void
    {
        $parent = Person::where('email', 'parent@kalinec.net')->first();
        $campus = Campus::first();
        $response = $this->actingAs($parent)
            ->post($this->store($campus), $this->data());
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('subjects', ['name' => $this->data()['name']]);
    }

    public function test_store_validation_failures(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $campus = Campus::first();

        // Empty name
        $this->actingAs($admin)
            ->post($this->store($campus), ['name' => '', 'color' => '#000000'])
            ->assertSessionHasErrors(['name']);

        // Name too long
        $this->actingAs($admin)
            ->post($this->store($campus), ['name' => str_repeat('a', 256), 'color' => '#000000'])
            ->assertSessionHasErrors(['name']);

        // Invalid color
        $this->actingAs($admin)
            ->post($this->store($campus), ['name' => 'Valid Name', 'color' => 'invalid'])
            ->assertSessionHasErrors(['color']);

        // Missing color
        $this->actingAs($admin)
            ->post($this->store($campus), ['name' => 'Valid Name'])
            ->assertSessionHasErrors(['color']);
    }
}
