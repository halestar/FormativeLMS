<?php

namespace Tests\Feature\Roles;

use App\Models\Utilities\SchoolPermission;
use App\Models\Utilities\SchoolRoles;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class CreateTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function index(): string
	{
		return route('settings.roles.index');
	}

	protected function store(): string
	{
		return route('settings.roles.store');
	}

	protected function data(): array
	{
		return [
			'name'        => 'Test Role',
			'permissions' => SchoolPermission::inRandomOrder()->take(3)->get()->pluck('id')->toArray(),
		];
	}

	public function test_store_admin(): void
	{
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $this->data());

		$response->assertRedirect($this->index());
		$this->assertDatabaseHas('roles', ['name' => $this->data()['name']]);
	}

	public function test_store_staff(): void
	{
		$response = $this->actingAsServiceAccount('staff@kalinec.net')
		                 ->post($this->store(), $this->data());

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('roles', ['name' => $this->data()['name']]);
	}

	public function test_store_faculty(): void
	{
		$response = $this->actingAsServiceAccount('faculty@kalinec.net')
		                 ->post($this->store(), $this->data());

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('roles', ['name' => $this->data()['name']]);
	}

	public function test_store_student(): void
	{
		$response = $this->actingAsServiceAccount('student@kalinec.net')
		                 ->post($this->store(), $this->data());

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('roles', ['name' => $this->data()['name']]);
	}

	public function test_store_parent(): void
	{
		$response = $this->actingAsServiceAccount('parent@kalinec.net')
		                 ->post($this->store(), $this->data());

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('roles', ['name' => $this->data()['name']]);
	}

	public function test_store_guest(): void
	{
		$data = ['permissions' => SchoolPermission::inRandomOrder()->take(3)->get()->pluck('id')->toArray()];
		$response = $this->post($this->store(), $this->data());

		$response->assertRedirect(route('login'));
	}

	/**
	 * Tests that storing a role fails when the name is missing.
	 */
	public function test_store_requires_name(): void
	{
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), []);

		$response->assertRedirectBackWithErrors(['name']);
	}

	/**
	 * Tests that storing a role fails when the name already exists.
	 */
	public function test_store_rejects_duplicate_name(): void
	{
		$data = $this->data();
		$data['name'] = SchoolRoles::inRandomOrder()->first()->name;
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['name']);
	}

	/**
	 * Tests that storing a role fails when the name exceeds the maximum length.
	 */
	public function test_store_rejects_overlong_name(): void
	{
		$data = $this->data();
		$data['name'] = str_repeat('a', 256);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['name']);
		$this->assertDatabaseMissing('roles', ['name' => $data['name']]);
	}

	/**
	 * Tests that storing a role fails when permissions are missing.
	 */
	public function test_store_requires_permissions(): void
	{
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), ['name' => 'Test Role']);
		$response->assertRedirectBackWithErrors(['permissions']);
		$this->assertDatabaseMissing('roles', ['name' => 'Test Role']);
	}

	/**
	 * Tests that storing a role fails when permissions is not an array.
	 */
	public function test_store_requires_permissions_to_be_array(): void
	{
		$data = $this->data();
		$data['permissions'] = 'not an array';
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['permissions']);
		$this->assertDatabaseMissing('roles', ['name' => $this->data()['name']]);
	}

	/**
	 * Tests that storing a role fails when no permissions are provided.
	 */
	public function test_store_requires_at_least_one_permission(): void
	{
		$data = $this->data();
		$data['permissions'] = [];
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['permissions']);
		$this->assertDatabaseMissing('roles', ['name' => $this->data()['name']]);
	}

	/**
	 * Tests that storing a role fails when a permission ID does not exist.
	 */
	public function test_store_rejects_unknown_permission_id(): void
	{
		$data = $this->data();
		$data['permissions'] = [10000];
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['permissions.0']);
		$this->assertDatabaseMissing('roles', ['name' => $this->data()['name']]);
	}

	/**
	 * Tests that storing a role syncs the provided permissions after creation.
	 */
	public function test_store_syncs_permissions_when_permissions_are_provided(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirect($this->index());
		$this->assertDatabaseHas('roles', ['name' => $this->data()['name']]);
		$newRole = SchoolRoles::where('name', $data['name'])->first();
		$this->assertNotNull($newRole);
		$this->assertCount(3, $newRole->permissions);
		$this->assertEqualsCanonicalizing($data['permissions'], $newRole->permissions->pluck('id')->toArray());
	}
}
