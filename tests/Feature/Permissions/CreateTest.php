<?php

namespace Tests\Feature\Permissions;

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
		return route('settings.permissions.index');
	}

	protected function store(): string
	{
		return route('settings.permissions.store');
	}

	protected function data(): array
	{
		return [
			'category_id' => 1,
			'name'        => 'test.permission',
			'description' => 'This is a test permission.',
		];
	}

	public function test_store_admin(): void
	{
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $this->data());

		$response->assertRedirect($this->index());
		$this->assertDatabaseHas('permissions', $this->data());
	}

	public function test_store_staff(): void
	{
		$response = $this->actingAsServiceAccount('staff@kalinec.net')
		                 ->post($this->store(), $this->data());

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', $this->data());
	}

	public function test_store_faculty(): void
	{
		$response = $this->actingAsServiceAccount('faculty@kalinec.net')
		                 ->post($this->store(), $this->data());

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', $this->data());
	}

	public function test_store_student(): void
	{
		$response = $this->actingAsServiceAccount('student@kalinec.net')
		                 ->post($this->store(), $this->data());

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', $this->data());
	}

	public function test_store_parent(): void
	{
		$response = $this->actingAsServiceAccount('parent@kalinec.net')
		                 ->post($this->store(), $this->data());

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', $this->data());
	}

	public function test_store_guest(): void
	{
		$response = $this->post($this->store(), $this->data());

		$response->assertRedirect(route('login'));
		$this->assertDatabaseMissing('permissions', $this->data());
	}

	/**
	 * Tests that storing a permission fails when the category ID is missing.
	 */
	public function test_store_requires_category_id(): void
	{
		$data = [
			'name'        => 'test.permission',
			'description' => 'This is a test permission.',
		];
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['category_id']);
		$this->assertDatabaseMissing('permissions', $data);
	}

	/**
	 * Tests that storing a permission fails when the category ID is not numeric.
	 */
	public function test_store_rejects_non_numeric_category_id(): void
	{
		$data = [
			'category_id' => 'notnumeric',
			'name'        => 'test.permission',
			'description' => 'This is a test permission.',
		];
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['category_id']);
		$this->assertDatabaseMissing('permissions', $data);
	}

	/**
	 * Tests that storing a permission fails when the category ID does not exist.
	 */
	public function test_store_rejects_unknown_category_id(): void
	{
		$data = [
			'category_id' => 10000,
			'name'        => 'test.permission',
			'description' => 'This is a test permission.',
		];
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['category_id']);
		$this->assertDatabaseMissing('permissions', $data);
	}

	/**
	 * Tests that storing a permission fails when the name is missing.
	 */
	public function test_store_requires_name(): void
	{
		$data = [
			'category_id' => 1,
			'description' => 'This is a test permission.',
		];
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['name']);
		$this->assertDatabaseMissing('permissions', $data);
	}

	/**
	 * Tests that storing a permission fails when the name already exists.
	 */
	public function test_store_rejects_duplicate_name(): void
	{
		$data = [
			'category_id' => 1,
			'name'        => 'locations.rooms',
			'description' => 'This is a test permission.',
		];
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['name']);
		$this->assertDatabaseMissing('permissions', $data);
	}

	/**
	 * Tests that storing a permission fails when the name exceeds the maximum length.
	 */
	public function test_store_rejects_overlong_name(): void
	{
		$data = [
			'category_id' => 1,
			'name'        => 'locations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.roomslocations.rooms',
			'description' => 'This is a test permission.',
		];
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['name']);
		$this->assertDatabaseMissing('permissions', $data);
	}

	/**
	 * Tests that storing a permission fails when the description is missing.
	 */
	public function test_store_requires_description(): void
	{
		$data = [
			'category_id' => 1,
			'name'        => 'test.permission',
		];
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['description']);
		$this->assertDatabaseMissing('permissions', $data);
	}

	/**
	 * Tests that storing a permission fails when the description is too short.
	 */
	public function test_store_rejects_short_description(): void
	{
		$data = [
			'category_id' => 1,
			'name'        => 'test.permission',
			'description' => 't',
		];
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['description']);
		$this->assertDatabaseMissing('permissions', $data);
	}

	/**
	 * Tests that storing a permission fails when the description exceeds the maximum length.
	 */
	public function test_store_rejects_overlong_description(): void
	{
		$data = [
			'category_id' => 1,
			'name'        => 'test.permission',
		];
		$data['description'] = str_repeat('test', 256);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['description']);
		$this->assertDatabaseMissing('permissions', $data);
	}

	/**
	 * Tests that storing a permission fails when roles is not an array.
	 */
	public function test_store_requires_roles_to_be_array(): void
	{
		$data = $this->data();
		$data['roles'] = 'none';
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['roles']);
		$this->assertDatabaseMissing('permissions', $this->data());
	}

	/**
	 * Tests that storing a permission syncs the provided roles after creation.
	 */
	public function test_store_syncs_roles_when_roles_are_provided(): void
	{
		$data = $this->data();
		$data['roles'] = SchoolRoles::inRandomOrder()->take(2)->get()->pluck('id')->toArray();
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirect($this->index());
		$this->assertDatabaseHas('permissions', $this->data());
		$newPermission = SchoolPermission::where('name', $data['name'])->first();
		$this->assertNotNull($newPermission);
		$this->assertCount(2, $newPermission->roles);
		$this->assertEqualsCanonicalizing($data['roles'], $newPermission->roles->pluck('id')->toArray());
	}

}
