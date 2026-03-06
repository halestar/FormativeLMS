<?php

namespace Feature\Controllers\Years;

use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\People\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class YearsTermCreatingTest extends TestCase
{
	use DatabaseTransactions;

	protected function store(Year $year): string
	{
		return route('locations.years.terms.store', $year);
	}

	protected function data(Year $year, Campus $campus): array
	{
		return [
			'term_label' => 'Fall Term',
			'campus_id' => $campus->id,
			'term_start' => $year->year_start->format(config('lms.date_format')),
			'term_end' => $year->year_end->format(config('lms.date_format')),
		];
	}

	public function test_store_term_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$year = Year::first();
		$campus = Campus::first();
		$response = $this->actingAs($admin)
			->post($this->store($year), $this->data($year, $campus));
		$response->assertRedirect();
		$this->assertDatabaseHas('terms', [
			'year_id' => $year->id,
			'campus_id' => $campus->id,
			'label' => 'Fall Term',
		]);
	}

	public function test_store_term_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$year = Year::first();
		$campus = Campus::first();
		$response = $this->actingAs($staff)
			->post($this->store($year), $this->data($year, $campus));
		$response->assertRedirect();
		$this->assertDatabaseHas('terms', [
			'year_id' => $year->id,
			'campus_id' => $campus->id,
			'label' => 'Fall Term',
		]);
	}

	public function test_store_term_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$year = Year::first();
		$campus = Campus::first();
		$response = $this->actingAs($faculty)
			->post($this->store($year), $this->data($year, $campus));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_store_term_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$year = Year::first();
		$campus = Campus::first();
		$response = $this->actingAs($student)
			->post($this->store($year), $this->data($year, $campus));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_store_term_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$year = Year::first();
		$campus = Campus::first();
		$response = $this->actingAs($parent)
			->post($this->store($year), $this->data($year, $campus));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}
}
