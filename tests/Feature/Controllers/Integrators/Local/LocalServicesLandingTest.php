<?php

namespace Feature\Controllers\Integrators\Local;

use App\Models\People\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LocalServicesLandingTest extends TestCase
{
	use DatabaseTransactions;

	protected function auth(): string
	{
		return route('integrators.local.auth.index');
	}

	protected function documents(): string
	{
		return route('integrators.local.documents.index');
	}

	protected function classes(): string
	{
		return route('integrators.local.classes.index');
	}

	protected function work(): string
	{
		return route('integrators.local.work.index');
	}

	/**
	 * index
	 */
	public function test_auth_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->auth());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_auth_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
        $response = $this->actingAs($staff)
	        ->get($this->auth());
        $response->assertStatus(Response::HTTP_OK);
    }

	public function test_auth_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->auth());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_auth_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->auth());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_auth_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->auth());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_auth_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->auth());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	/**
	 * documents
	 */
	public function test_documents_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->documents());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_documents_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->get($this->documents());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_documents_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->documents());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_documents_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->documents());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_documents_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->documents());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_documents_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->documents());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	/**
	 * classes
	 */
	public function test_classes_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->classes());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_classes_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->get($this->classes());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_classes_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->classes());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_classes_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->classes());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_classes_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->classes());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_classes_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->classes());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	/**
	 * work
	 */
	public function test_work_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->work());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_work_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->get($this->work());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_work_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->work());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_work_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->work());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_work_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->work());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_work_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->work());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}
}
