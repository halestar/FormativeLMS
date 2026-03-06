<?php

namespace Feature\Controllers\Campuses;

use App\Models\Locations\Campus;
use App\Models\People\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CampusesDeletingTest extends TestCase
{
    use DatabaseTransactions;

    protected function index(): string
    {
        return route('locations.campuses.index');
    }

    protected function deleteRoute($id): string
    {
        return route('locations.campuses.destroy', $id);
    }

    public function test_delete_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $campus = new Campus;
        $campus->name = 'Temp Campus';
        $campus->abbr = 'TC';
        $campus->save();

        $this->actingAs($admin)
            ->delete($this->deleteRoute($campus->id))
            ->assertRedirect($this->index());

        $this->assertDatabaseMissing('campuses', ['id' => $campus->id]);
    }

    public function test_delete_staff(): void
    {
        $staff = Person::where('email', 'staff@kalinec.net')->first();
        $campus = new Campus;
        $campus->name = 'Temp Campus';
        $campus->abbr = 'TC';
        $campus->save();

        $this->actingAs($staff)
            ->delete($this->deleteRoute($campus->id))
            ->assertRedirect($this->index());

        $this->assertDatabaseMissing('campuses', ['id' => $campus->id]);
    }

    public function test_delete_faculty(): void
    {
        $faculty = Person::where('email', 'faculty@kalinec.net')->first();
        $campus = new Campus;
        $campus->name = 'Temp Campus';
        $campus->abbr = 'TC';
        $campus->save();

        $this->actingAs($faculty)
            ->delete($this->deleteRoute($campus->id))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('campuses', ['id' => $campus->id]);
    }

    public function test_delete_student(): void
    {
        $student = Person::where('email', 'student@kalinec.net')->first();
        $campus = new Campus;
        $campus->name = 'Temp Campus';
        $campus->abbr = 'TC';
        $campus->save();

        $this->actingAs($student)
            ->delete($this->deleteRoute($campus->id))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('campuses', ['id' => $campus->id]);
    }

    public function test_delete_parent(): void
    {
        $parent = Person::where('email', 'parent@kalinec.net')->first();
        $campus = new Campus;
        $campus->name = 'Temp Campus';
        $campus->abbr = 'TC';
        $campus->save();

        $this->actingAs($parent)
            ->delete($this->deleteRoute($campus->id))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('campuses', ['id' => $campus->id]);
    }
}
