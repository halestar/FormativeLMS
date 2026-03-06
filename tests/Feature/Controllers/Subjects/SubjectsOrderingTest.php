<?php

namespace Feature\Controllers\Subjects;

use App\Models\Locations\Campus;
use App\Models\People\Person;
use App\Models\SubjectMatter\Subject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SubjectsOrderingTest extends TestCase
{
    use DatabaseTransactions;

    protected function url(): string
    {
        return route('subjects.subjects.update.order');
    }

    public function test_order_admin(): void
    {
        $admin = Person::where('email', 'fablms@kalinec.net')->first();
        $campus = Campus::first();

        // Create a few subjects to reorder
        $s1 = Subject::create(['name' => 'S1', 'color' => '#000000', 'campus_id' => $campus->id, 'order' => 1]);
        $s2 = Subject::create(['name' => 'S2', 'color' => '#000000', 'campus_id' => $campus->id, 'order' => 2]);
        $s3 = Subject::create(['name' => 'S3', 'color' => '#000000', 'campus_id' => $campus->id, 'order' => 3]);

        $response = $this->actingAs($admin)
            ->put($this->url(), ['subjects' => json_encode([$s3->id, $s1->id, $s2->id])]);

        $response->assertRedirect();
        $this->assertEquals(1, $s3->fresh()->order);
        $this->assertEquals(2, $s1->fresh()->order);
        $this->assertEquals(3, $s2->fresh()->order);
    }

    public function test_order_staff(): void
    {
        $staff = Person::where('email', 'staff@kalinec.net')->first();
        $campus = Campus::first();

        $s1 = Subject::create(['name' => 'S1', 'color' => '#000000', 'campus_id' => $campus->id, 'order' => 1]);
        $s2 = Subject::create(['name' => 'S2', 'color' => '#000000', 'campus_id' => $campus->id, 'order' => 2]);

        $response = $this->actingAs($staff)
            ->put($this->url(), ['subjects' => json_encode([$s2->id, $s1->id])]);

        $response->assertRedirect();
        $this->assertEquals(1, $s2->fresh()->order);
        $this->assertEquals(2, $s1->fresh()->order);
    }

    public function test_order_faculty(): void
    {
        $faculty = Person::where('email', 'faculty@kalinec.net')->first();
        $campus = Campus::first();

        $s1 = Subject::create(['name' => 'S1', 'color' => '#000000', 'campus_id' => $campus->id, 'order' => 1]);
        $s2 = Subject::create(['name' => 'S2', 'color' => '#000000', 'campus_id' => $campus->id, 'order' => 2]);

        $response = $this->actingAs($faculty)
            ->put($this->url(), ['subjects' => json_encode([$s2->id, $s1->id])]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertEquals(1, $s1->fresh()->order);
        $this->assertEquals(2, $s2->fresh()->order);
    }

    public function test_order_student(): void
    {
        $student = Person::where('email', 'student@kalinec.net')->first();
        $campus = Campus::first();

        $s1 = Subject::create(['name' => 'S1', 'color' => '#000000', 'campus_id' => $campus->id, 'order' => 1]);
        $s2 = Subject::create(['name' => 'S2', 'color' => '#000000', 'campus_id' => $campus->id, 'order' => 2]);

        $response = $this->actingAs($student)
            ->put($this->url(), ['subjects' => json_encode([$s2->id, $s1->id])]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_order_parent(): void
    {
        $parent = Person::where('email', 'parent@kalinec.net')->first();
        $campus = Campus::first();

        $s1 = Subject::create(['name' => 'S1', 'color' => '#000000', 'campus_id' => $campus->id, 'order' => 1]);
        $s2 = Subject::create(['name' => 'S2', 'color' => '#000000', 'campus_id' => $campus->id, 'order' => 2]);

        $response = $this->actingAs($parent)
            ->put($this->url(), ['subjects' => json_encode([$s2->id, $s1->id])]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
