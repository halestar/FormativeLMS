<?php

namespace Tests\Feature\Rooms;

use App\Models\Locations\Room;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class CreateTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function edit($id): string
	{
		return route('locations.rooms.edit', $id);
	}

	protected function store(): string
	{
		return route('locations.rooms.store');
	}

	protected function data(): array
	{
		return [
			'name'     => 'New Room',
			'capacity' => 25,
			'area_id'  => null,
		];
	}

	public function test_store_admin(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$this->assertDatabaseHas('rooms',
			[
				'name'     => $data['name'],
				'capacity' => $data['capacity'],
				'area_id'  => $data['area_id'],
				'img_data' => null,
				'phone_id' => null,
				'notes'    => null,
			]);
		$newRoom = Room::where('name', $data['name'])->first();
		$response->assertRedirect($this->edit($newRoom->id));
	}

	public function test_store_staff(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('staff@kalinec.net')
		                 ->post($this->store(), $data);

		$this->assertDatabaseHas('rooms',
			[
				'name'     => $data['name'],
				'capacity' => $data['capacity'],
				'area_id'  => $data['area_id'],
				'img_data' => null,
				'phone_id' => null,
				'notes'    => null,
			]);
		$newRoom = Room::where('name', $data['name'])->first();
		$response->assertRedirect($this->edit($newRoom->id));
	}

	public function test_store_faculty(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('faculty@kalinec.net')
		                 ->post($this->store(), $data);


		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('rooms',
			[
				'name'     => $data['name'],
				'capacity' => $data['capacity'],
				'area_id'  => $data['area_id'],
			]);
	}

	public function test_store_student(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('student@kalinec.net')
		                 ->post($this->store(), $data);


		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('rooms',
			[
				'name'     => $data['name'],
				'capacity' => $data['capacity'],
				'area_id'  => $data['area_id'],
			]);
	}

	public function test_store_parent(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('parent@kalinec.net')
		                 ->post($this->store(), $data);


		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('rooms',
			[
				'name'     => $data['name'],
				'capacity' => $data['capacity'],
				'area_id'  => $data['area_id'],
			]);
	}

	public function test_store_guest(): void
	{
		$data = $this->data();
		$response = $this->post($this->store(), $data);

		$response->assertRedirect(route('login'));
	}

	/**
	 * Test that storing a room requires a name.
	 */
	public function test_store_requires_name(): void
	{
		$data = $this->data();
		unset($data['name']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['name']);
	}

	/**
	 * Test that storing a room requires a capacity.
	 */
	public function test_store_requires_capacity(): void
	{
		$data = $this->data();
		unset($data['capacity']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['capacity']);
	}

	/**
	 * Test that storing a room requires the capacity to be numeric.
	 */
	public function test_store_requires_capacity_to_be_numeric(): void
	{
		$data = $this->data();
		$data['capacity'] = "number";
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['capacity']);
	}

	/**
	 * Test that storing a room requires the capacity to be at least one.
	 */
	public function test_store_requires_capacity_to_be_at_least_one(): void
	{
		$data = $this->data();
		$data['capacity'] = 0;
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['capacity']);
	}

	/**
	 * Test that storing a room requires the capacity to be at most 10000.
	 */
	public function test_store_requires_capacity_to_be_at_most_10000(): void
	{
		$data = $this->data();
		$data['capacity'] = 10001;
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['capacity']);
	}

	/**
	 * Test that storing a room requires area_id to reference an existing building area.
	 */
	public function test_store_requires_area_id_to_exist(): void
	{
		$data = $this->data();
		$data['area_id'] = 10001;
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['area_id']);
	}
}
