<?php

namespace Feature\Roles;

use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RolesCreatingTest extends TestCase
{

	use DatabaseTransactions;

	protected function index(): string
	{
		return route('settings.roles.index');
	}

	protected function create(): string
	{
		return route('settings.roles.create');
	}

	protected function store()
	{
		return route('settings.roles.store');
	}

	protected function data(): array
	{
		return
		[
			'name' => 'New Role',
			'permissions' => [1,2,3],
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


	public function test_store_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->post($this->store(), $this->data());
		$response->assertRedirect($this->index());
		$this->assertDatabaseHas('roles', ['name' => $this->data()['name']]);
		$model = SchoolRoles::where('name', $this->data()['name'])->first();
		$this->assertInstanceOf(SchoolRoles::class, $model);
		$this->assertInstanceOf(BelongsToMany::class, $model->permissions());
		$this->assertTrue($model->permissions()->where('id', 1)->exists());
		$this->assertTrue($model->permissions()->where('id', 2)->exists());
		$this->assertTrue($model->permissions()->where('id', 3)->exists());
	}

	public function test_store_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->post($this->store(), $this->data());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', ['name' => $this->data()['name']]);
	}

	public function test_store_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->post($this->store(), $this->data());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', ['name' => $this->data()['name']]);
	}

	public function test_store_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->post($this->store(), $this->data());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', ['name' => $this->data()['name']]);
	}

	public function test_store_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->post($this->store(), $this->data());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', ['name' => $this->data()['name']]);
	}

}
