<?php

namespace Feature\Controllers\Integrators;

use App\Classes\Integrators\Local\LocalIntegrator;
use App\Enums\IntegratorServiceTypes;
use App\Models\People\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class IntegratorsLandingTest extends TestCase
{
	use DatabaseTransactions;

	protected function index(): string
	{
		return route('integrators.index');
	}

	protected function permissions(): string
	{
		$service = LocalIntegrator::getService(IntegratorServiceTypes::AUTHENTICATION);
		return route('integrators.services.permissions', $service);
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
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	/**
	 * permissions
	 */
	public function test_permissions_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->permissions());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_permissions_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->get($this->permissions());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_permissions_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->permissions());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_permissions_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->permissions());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_permissions_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->permissions());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_permissions_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->permissions());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}
}
