<?php

namespace Feature\Controllers\Campuses;

use App\Models\Locations\Campus;
use App\Models\People\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CampusesCreatingTest extends TestCase
{
    use DatabaseTransactions;

    protected function index(): string
    {
        return route('locations.campuses.index');
    }

    protected function store(): string
    {
        return route('locations.campuses.store');
    }

    protected function data(): array
    {
        return [
            'name' => 'New Campus',
            'abbr' => 'NC',
        ];
    }

    public function test_store_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $response = $this->actingAs($admin)
            ->post($this->store(), $this->data());

        $campus = Campus::where('name', 'New Campus')->first();
        $response->assertRedirect(route('locations.campuses.edit', $campus));
        $this->assertDatabaseHas('campuses', ['name' => 'New Campus']);
    }

    public function test_store_staff(): void
    {
        $staff = Person::where('email', 'staff@kalinec.net')->first();
        $response = $this->actingAs($staff)
            ->post($this->store(), $this->data());

        $campus = Campus::where('name', 'New Campus')->first();
        $response->assertRedirect(route('locations.campuses.edit', $campus));
        $this->assertDatabaseHas('campuses', ['name' => 'New Campus']);
    }

    public function test_store_faculty(): void
    {
        $faculty = Person::where('email', 'faculty@kalinec.net')->first();
        $response = $this->actingAs($faculty)
            ->post($this->store(), $this->data());

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('campuses', ['name' => 'New Campus']);
    }

    public function test_store_student(): void
    {
        $student = Person::where('email', 'student@kalinec.net')->first();
        $response = $this->actingAs($student)
            ->post($this->store(), $this->data());

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('campuses', ['name' => 'New Campus']);
    }

    public function test_store_parent(): void
    {
        $parent = Person::where('email', 'parent@kalinec.net')->first();
        $response = $this->actingAs($parent)
            ->post($this->store(), $this->data());

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('campuses', ['name' => 'New Campus']);
    }
}
