<?php

namespace Feature\Controllers\Campuses;

use App\Models\Locations\Campus;
use App\Models\People\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CampusesEditingTest extends TestCase
{
    use DatabaseTransactions;

    protected function index(): string
    {
        return route('locations.campuses.index');
    }

    protected function edit($id): string
    {
        return route('locations.campuses.edit', $id);
    }

    protected function update($id): string
    {
        return route('locations.campuses.update.basic', $id);
    }

    protected function data(Campus $campus): array
    {
        return [
            'name' => $campus->name.'_new',
            'abbr' => 'NC',
            'title' => 'New Title',
        ];
    }

    public function test_edit_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $campus = Campus::first();
        $this->actingAs($admin)
            ->get($this->edit($campus->id))
            ->assertStatus(Response::HTTP_OK)
            ->assertSee($campus->name);
    }

    public function test_edit_staff(): void
    {
        $staff = Person::where('email', 'staff@kalinec.net')->first();
        $campus = Campus::first();
        $this->actingAs($staff)
            ->get($this->edit($campus->id))
            ->assertStatus(Response::HTTP_OK)
            ->assertSee($campus->name);
    }

    public function test_edit_faculty(): void
    {
        $faculty = Person::where('email', 'faculty@kalinec.net')->first();
        $campus = Campus::first();
        $response = $this->actingAs($faculty)
            ->get($this->edit($campus->id));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_edit_student(): void
    {
        $student = Person::where('email', 'student@kalinec.net')->first();
        $campus = Campus::first();
        $response = $this->actingAs($student)
            ->get($this->edit($campus->id));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_edit_parent(): void
    {
        $parent = Person::where('email', 'parent@kalinec.net')->first();
        $campus = Campus::first();
        $response = $this->actingAs($parent)
            ->get($this->edit($campus->id));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_update_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $campus = Campus::first();
        $data = $this->data($campus);
        $response = $this->actingAs($admin)
            ->put($this->update($campus->id), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('campuses', [
            'id' => $campus->id,
            'name' => $data['name'],
        ]);
    }

    public function test_update_staff(): void
    {
        $staff = Person::where('email', 'staff@kalinec.net')->first();
        $campus = Campus::first();
        $data = $this->data($campus);
        $response = $this->actingAs($staff)
            ->put($this->update($campus->id), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('campuses', [
            'id' => $campus->id,
            'name' => $data['name'],
        ]);
    }

    public function test_update_faculty(): void
    {
        $faculty = Person::where('email', 'faculty@kalinec.net')->first();
        $campus = Campus::first();
        $data = $this->data($campus);
        $response = $this->actingAs($faculty)
            ->put($this->update($campus->id), $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('campuses', [
            'id' => $campus->id,
            'name' => $data['name'],
        ]);
    }

    public function test_update_student(): void
    {
        $student = Person::where('email', 'student@kalinec.net')->first();
        $campus = Campus::first();
        $data = $this->data($campus);
        $response = $this->actingAs($student)
            ->put($this->update($campus->id), $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('campuses', [
            'id' => $campus->id,
            'name' => $data['name'],
        ]);
    }

    public function test_update_parent(): void
    {
        $parent = Person::where('email', 'parent@kalinec.net')->first();
        $campus = Campus::first();
        $data = $this->data($campus);
        $response = $this->actingAs($parent)
            ->put($this->update($campus->id), $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('campuses', [
            'id' => $campus->id,
            'name' => $data['name'],
        ]);
    }

    public function test_update_levels_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $campus = Campus::first();
        $level = \App\Models\SystemTables\Level::first();

        $response = $this->actingAs($admin)
            ->put(route('locations.campuses.update.levels', $campus->id), [
                'levels' => [$level->id],
            ]);

        $response->assertRedirect();
        $this->assertTrue($campus->levels()->where('system_table_id', $level->id)->exists());
    }

    public function test_update_levels_forbidden(): void
    {
        $faculty = Person::where('email', 'faculty@kalinec.net')->first();
        $campus = Campus::first();
        $level = \App\Models\SystemTables\Level::first();

        $response = $this->actingAs($faculty)
            ->put(route('locations.campuses.update.levels', $campus->id), [
                'levels' => [$level->id],
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_update_icon_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $campus = Campus::first();

        $response = $this->actingAs($admin)
            ->put(route('locations.campuses.update.icon', $campus->id), [
                'color_pri' => '#123456',
                'color_sec' => '#654321',
            ]);

        $response->assertRedirect();
        $campus->refresh();
        $this->assertEquals('#123456', $campus->color_pri);
        $this->assertEquals('#654321', $campus->color_sec);
    }

    public function test_update_order_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $campus = Campus::first();

        $response = $this->actingAs($admin)
            ->get(route('locations.campuses.update.order', ['campus' => $campus->id, 'order' => 2]));

        $response->assertRedirect();
        $campus->refresh();
        $this->assertEquals(2, $campus->order);
    }
}
