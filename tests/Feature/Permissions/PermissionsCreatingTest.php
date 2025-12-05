<?php

namespace Tests\Feature\Permissions;

use App\Models\People\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PermissionsCreatingTest extends TestCase
{

	use DatabaseTransactions;

	protected function index(): string
	{
		return route('settings.permissions.index');
	}

	protected function create(): string
	{
		return route('settings.permissions.create');
	}

	protected function store()
	{
		return route('settings.permissions.store');
	}

	protected function data(): array
	{
		return
		[
			'category_id' => 1,
			'name' => 'test.permission',
			'description' => 'This is a test permission.',
		];
	}

    /**
     * A basic feature test example.
     */
    public function test_create_admin(): void
    {
	    $admin = Person::where('email', 'fablms@kalinec.net')->first();
	    $response = $this->actingAs($admin)
		    ->get($this->create());
	    $response->assertStatus(Response::HTTP_OK);

        $response->assertStatus(Response::HTTP_OK);
    }

	public function test_create_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->get($this->create());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	/**
	 * A basic feature test example.
	 */
	public function test_create_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->create());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	/**
	 * A basic feature test example.
	 */
	public function test_create_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->create());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	/**
	 * A basic feature test example.
	 */
	public function test_create_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->create());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	/**
	 * A basic feature test example.
	 */
	public function test_create_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->create());
		$response->assertRedirect(route('login'));
	}

	public function test_store_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->post($this->store(), $this->data());
		$response->assertRedirect($this->index());
		$this->assertDatabaseHas('permissions', $this->data());
	}

	public function test_store_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->post($this->store(), $this->data());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', $this->data());
	}

	public function test_store_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->post($this->store(), $this->data());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', $this->data());
	}

	public function test_store_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->post($this->store(), $this->data());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', $this->data());
	}

	public function test_store_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->post($this->store(), $this->data());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', $this->data());
	}

	public function test_store_guest(): void
	{
		$guest = Person::where('email', 'guest@kalinec.net')->first();
		$response = $this->actingAsGuest()
			->post($this->store(), $this->data());
		$response->assertRedirect(route('login'));
		$this->assertDatabaseMissing('permissions', $this->data());
	}

}
