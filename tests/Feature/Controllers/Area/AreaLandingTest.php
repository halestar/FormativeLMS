<?php

namespace Feature\Controllers\Area;

use App\Models\Locations\BuildingArea;
use App\Models\People\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AreaLandingTest extends TestCase
{
	use DatabaseTransactions;

	protected function index(): string
	{
		$area = BuildingArea::inRandomOrder()->first();
		return route('locations.areas.show', $area);
	}

	protected function maps(): string
	{
		$area = BuildingArea::inRandomOrder()->first();
		return route('locations.maps.area', $area);
	}

	/**
	 * index
	 */
	public function test_landing_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->index());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_landing_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
        $response = $this->actingAs($staff)
	        ->get($this->index());
        $response->assertStatus(Response::HTTP_OK);
    }

	public function test_landing_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->index());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_landing_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->index());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_landing_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->index());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_landing_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->index());
		$response->assertRedirect(route('login'));
	}

	/**
	 * maps
	 */
	public function test_maps_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->maps());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_maps_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->get($this->maps());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_maps_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->maps());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_maps_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->maps());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_maps_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->maps());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_maps_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->maps());
		$response->assertRedirect(route('login'));
	}
}
