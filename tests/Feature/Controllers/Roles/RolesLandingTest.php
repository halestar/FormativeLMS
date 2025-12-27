<?php

namespace Feature\Controllers\Roles;

use App\Models\People\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RolesLandingTest extends TestCase
{
	use DatabaseTransactions;

	private function url(): string
	{
		return route('settings.roles.index');
	}

	/**
	 * A basic feature test example.
	 */
	public function test_landing_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->url());
		$response->assertStatus(Response::HTTP_OK);
	}

    /**
     * A basic feature test example.
     */
    public function test_landing_staff(): void
    {
		$staff = Person::where('email', 'staff@kalinec.net')->first();
        $response = $this->actingAs($staff)
	        ->get($this->url());
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

	/**
	 * A basic feature test example.
	 */
	public function test_landing_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->url());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	/**
	 * A basic feature test example.
	 */
	public function test_landing_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->url());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	/**
	 * A basic feature test example.
	 */
	public function test_landing_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->url());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	/**
	 * A basic feature test example.
	 */
	public function test_landing_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->url());
		$response->assertStatus(Response::HTTP_FOUND);
	}
}
