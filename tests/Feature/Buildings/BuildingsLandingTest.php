<?php

namespace Feature\Buildings;

use App\Models\Locations\Building;
use App\Models\People\Person;
use Database\Seeders\DatabaseDumpSeeder;
use Database\Seeders\SmallDatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BuildingsLandingTest extends TestCase
{
	use DatabaseTransactions;

	protected function index(): string
	{
		return route('locations.buildings.index');
	}

	protected function show(): string
	{
		$building = Building::inRandomOrder()->first();
		return route('locations.buildings.show', $building);
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
	 * show
	 */
	public function test_show_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->show());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_show_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->get($this->show());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_show_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->show());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_show_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->show());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_show_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->show());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_show_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->show());
		$response->assertRedirect(route('login'));
	}
}
