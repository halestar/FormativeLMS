<?php

namespace Feature\Person;

use App\Classes\Integrators\Local\LocalIntegrator;
use App\Enums\IntegratorServiceTypes;
use App\Models\People\Person;
use Database\Seeders\DatabaseDumpSeeder;
use Database\Seeders\SmallDatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PersonLandingTest extends TestCase
{
	use DatabaseTransactions;

	protected function index(): string
	{
		return route('people.index');
	}

	protected function fieldPermissions(): string
	{
		return route('people.fields.permissions');
	}

	protected function passwordLanding(): string
	{
		return route('people.password');
	}

	protected function fieldLanding(): string
	{
		return route('people.roles.fields');
	}

	protected function connectToService(Person $person): void
	{
		$conn = LocalIntegrator::getService(IntegratorServiceTypes::AUTHENTICATION)->connect($person);
		$person->auth_connection_id = $conn->id;
		$person->save();
	}

	/**
	 * INDEX
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
		$response->assertStatus(Response::HTTP_OK);
	}


	public function test_landing_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->index());
		$response->assertStatus(Response::HTTP_OK);
	}


	public function test_landing_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->index());
		$response->assertStatus(Response::HTTP_OK);
	}


	public function test_landing_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->index());
		$response->assertRedirect(route('login'));
	}
	/**
	 * fieldPermissions
	 */
	public function test_field_permissions_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->fieldPermissions());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_field_permissions_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->get($this->fieldPermissions());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_field_permissions_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->fieldPermissions());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}


	public function test_field_permissions_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->fieldPermissions());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}


	public function test_field_permissions_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->fieldPermissions());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}


	public function test_field_permissions_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->fieldPermissions());
		$response->assertRedirect(route('login'));
	}




	/**
	 * password landing
	 */
	public function test_password_landing_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$this->connectToService($admin);
		$response = $this->actingAs($admin)
			->get($this->passwordLanding());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_password_landing_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$this->connectToService($staff);
		$response = $this->actingAs($staff)
			->get($this->passwordLanding());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_password_landing_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$this->connectToService($faculty);
		$response = $this->actingAs($faculty)
			->get($this->passwordLanding());
		$response->assertStatus(Response::HTTP_OK);
	}


	public function test_password_landing_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$this->connectToService($student);
		$response = $this->actingAs($student)
			->get($this->passwordLanding());
		$response->assertStatus(Response::HTTP_OK);
	}


	public function test_password_landing_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$this->connectToService($parent);
		$response = $this->actingAs($parent)
			->get($this->passwordLanding());
		$response->assertStatus(Response::HTTP_OK);
	}


	public function test_password_landing_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->passwordLanding());
		$response->assertRedirect(route('login'));
	}

	/**
	 * field landing
	 */
	public function test_field_landing_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->fieldLanding());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_field_landing_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->get($this->fieldLanding());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_field_landing_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->fieldLanding());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}


	public function test_field_landing_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->fieldLanding());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}


	public function test_field_landing_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->fieldLanding());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}


	public function test_field_landing_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->fieldLanding());
		$response->assertRedirect(route('login'));
	}

}
