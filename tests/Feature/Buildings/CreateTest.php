<?php

namespace Tests\Feature\Buildings;

use App\Models\Locations\Building;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class CreateTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function edit($id): string
	{
		return route('locations.buildings.edit', $id);
	}

	protected function store(): string
	{
		return route('locations.buildings.store');
	}

	protected function data(): array
	{
		return [
			'name' => 'Test Building',
		];
	}

	public function test_store_admin(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('staff@kalinec.net')
		                 ->post($this->store(), $data);

		$this->assertDatabaseHas('buildings', ['name' => $data['name']]);
		$newBuilding = Building::where('name', $data['name'])->first();
		$response->assertRedirect($this->edit($newBuilding->id));
	}

	public function test_store_staff(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('staff@kalinec.net')
		                 ->post($this->store(), $data);

		$this->assertDatabaseHas('buildings', ['name' => $data['name']]);
		$newBuilding = Building::where('name', $data['name'])->first();
		$response->assertRedirect($this->edit($newBuilding->id));
	}

	public function test_store_faculty(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('faculty@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('buildings', ['name' => $data['name']]);
	}

	public function test_store_student(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('student@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('buildings', ['name' => $data['name']]);
	}

	public function test_store_parent(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('parent@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('buildings', ['name' => $data['name']]);
	}

	public function test_store_guest(): void
	{
		$data = $this->data();
		$response = $this->post($this->store(), $data);
		$response->assertRedirect(route('login'));
	}

	/**
	 * Test that storing a building requires a name.
	 */
	public function test_store_requires_name(): void
	{
		$data = [];
		$response = $this->actingAsServiceAccount('staff@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['name']);
	}

	/**
	 * Test that storing a building rejects names longer than 255 characters.
	 */
	public function test_store_limits_name_to_255_characters(): void
	{
		$data = ['name' => str_repeat('a', 256)];
		$response = $this->actingAsServiceAccount('staff@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['name']);
		$this->assertDatabaseMissing('buildings', ['name' => $data['name']]);
	}
}
