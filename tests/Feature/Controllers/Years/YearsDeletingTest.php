<?php

namespace Feature\Controllers\Years;

use App\Models\Locations\Year;
use App\Models\People\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class YearsDeletingTest extends TestCase
{
	use DatabaseTransactions;

	protected function index(): string
	{
		return route('locations.years.index');
	}

	protected function destroy(Year $year): string
	{
		return route('locations.years.destroy', $year);
	}

	public function test_destroy_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$year = Year::create([
			'label' => 'Delete Me',
			'year_start' => '2020-01-01',
			'year_end' => '2020-12-31',
		]);
		$response = $this->actingAs($admin)
			->delete($this->destroy($year));
		$response->assertRedirect($this->index());
		$this->assertDatabaseMissing('years', ['id' => $year->id]);
	}

	public function test_destroy_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$year = Year::create([
			'label' => 'Delete Me Staff',
			'year_start' => '2020-01-01',
			'year_end' => '2020-12-31',
		]);
		$response = $this->actingAs($staff)
			->delete($this->destroy($year));
		$response->assertRedirect($this->index());
		$this->assertDatabaseMissing('years', ['id' => $year->id]);
	}

	public function test_destroy_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$year = Year::create([
			'label' => 'No Delete Faculty',
			'year_start' => '2020-01-01',
			'year_end' => '2020-12-31',
		]);
		$response = $this->actingAs($faculty)
			->delete($this->destroy($year));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseHas('years', ['id' => $year->id]);
	}

	public function test_destroy_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$year = Year::create([
			'label' => 'No Delete Student',
			'year_start' => '2020-01-01',
			'year_end' => '2020-12-31',
		]);
		$response = $this->actingAs($student)
			->delete($this->destroy($year));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseHas('years', ['id' => $year->id]);
	}

	public function test_destroy_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$year = Year::create([
			'label' => 'No Delete Parent',
			'year_start' => '2020-01-01',
			'year_end' => '2020-12-31',
		]);
		$response = $this->actingAs($parent)
			->delete($this->destroy($year));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseHas('years', ['id' => $year->id]);
	}
}
