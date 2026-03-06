<?php

namespace Feature\Controllers\Years;

use App\Models\Locations\Year;
use App\Models\People\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class YearsEditingTest extends TestCase
{
	use DatabaseTransactions;

	protected function index(): string
	{
		return route('locations.years.index');
	}

	protected function update(Year $year): string
	{
		return route('locations.years.update', $year);
	}

	protected function data(Year $year): array
	{
		return [
			'label' => $year->label . '_updated',
			'year_start' => $year->year_start->format(config('lms.date_format')),
			'year_end' => $year->year_end->format(config('lms.date_format')),
		];
	}

	public function test_update_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$year = Year::first();
		$response = $this->actingAs($admin)
			->put($this->update($year), $this->data($year));
		$response->assertRedirect();
		$this->assertDatabaseHas('years', ['id' => $year->id, 'label' => $year->label . '_updated']);
	}

	public function test_update_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$year = Year::first();
		$response = $this->actingAs($staff)
			->put($this->update($year), $this->data($year));
		$response->assertRedirect();
		$this->assertDatabaseHas('years', ['id' => $year->id, 'label' => $year->label . '_updated']);
	}

	public function test_update_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$year = Year::first();
		$response = $this->actingAs($faculty)
			->put($this->update($year), $this->data($year));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_update_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$year = Year::first();
		$response = $this->actingAs($student)
			->put($this->update($year), $this->data($year));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_update_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$year = Year::first();
		$response = $this->actingAs($parent)
			->put($this->update($year), $this->data($year));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}
}
