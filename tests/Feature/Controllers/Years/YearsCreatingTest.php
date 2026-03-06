<?php

namespace Feature\Controllers\Years;

use App\Models\Locations\Year;
use App\Models\People\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class YearsCreatingTest extends TestCase
{
	use DatabaseTransactions;

	protected function index(): string
	{
		return route('locations.years.index');
	}

	protected function store(): string
	{
		return route('locations.years.store');
	}

	protected function data(): array
	{
		return [
			'label' => '2125-2126',
			'year_start' => '2125-09-01',
			'year_end' => '2126-06-30',
		];
	}

	public function test_store_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->post($this->store(), $this->data());
		$response->assertRedirect();
		$this->assertDatabaseHas('years', ['label' => '2125-2126']);
	}

	public function test_store_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->post($this->store(), $this->data());
		$response->assertRedirect();
		$this->assertDatabaseHas('years', ['label' => '2125-2126']);
	}

	public function test_store_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->post($this->store(), $this->data());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('years', ['label' => '2125-2126']);
	}

	public function test_store_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->post($this->store(), $this->data());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('years', ['label' => '2125-2126']);
	}

	public function test_store_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->post($this->store(), $this->data());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('years', ['label' => '2125-2126']);
	}
}
