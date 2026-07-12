<?php

namespace Tests\Feature\Campuses;

use App\Models\Locations\Campus;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class CreateTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function edit($id): string
	{
		return route('locations.campuses.edit', $id);
	}

	protected function store(): string
	{
		return route('locations.campuses.store');
	}

	protected function data(): array
	{
		return [
			'name' => 'New Campus',
			'abbr' => 'NC',
		];
	}

	public function test_store_admin(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$this->assertDatabaseHas('campuses',
			[
				'name'        => $data['name'],
				'abbr'        => $data['abbr'],
				'title'       => null,
				'established' => null,
				'order'       => Campus::count() - 1,
				'img'         => null,
				'icon'        => null,
				'color_pri'   => '#000000',
				'color_sec'   => '#ffffff',
			]);
		$campus = Campus::where('name', 'New Campus')->first();
		$response->assertRedirect($this->edit($campus->id));
	}

	public function test_store_staff(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('staff@kalinec.net')
		                 ->post($this->store(), $data);

		$this->assertDatabaseHas('campuses',
			[
				'name'        => $data['name'],
				'abbr'        => $data['abbr'],
				'title'       => null,
				'established' => null,
				'order'       => Campus::count() - 1,
				'img'         => null,
				'icon'        => null,
				'color_pri'   => '#000000',
				'color_sec'   => '#ffffff',
			]);
		$campus = Campus::where('name', 'New Campus')->first();
		$response->assertRedirect($this->edit($campus->id));
	}

	public function test_store_faculty(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('faculty@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('campuses', ['name' => 'New Campus']);
	}

	public function test_store_student(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('student@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('campuses', ['name' => 'New Campus']);
	}

	public function test_store_parent(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('parent@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('campuses', ['name' => 'New Campus']);
	}

	public function test_store_guest(): void
	{
		$data = $this->data();
		$response = $this->post($this->store(), $data);

		$response->assertRedirect(route('login'));
	}

	/**
	 * Test that storing a campus requires a name.
	 */
	public function test_store_requires_name(): void
	{
		$data = $this->data();
		unset($data['name']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['name']);
		$this->assertDatabaseMissing('campuses',
			[
				'abbr' => $data['abbr'],
			]);
	}

	/**
	 * Test that storing a campus rejects names longer than 255 characters.
	 */
	public function test_store_limits_name_to_255_characters(): void
	{
		$data = $this->data();
		$data['name'] = str_repeat('a', 256);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['name']);
		$this->assertDatabaseMissing('campuses',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

	/**
	 * Test that storing a campus requires an abbreviation.
	 */
	public function test_store_requires_abbr(): void
	{
		$data = $this->data();
		unset($data['abbr']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['abbr']);
		$this->assertDatabaseMissing('campuses',
			[
				'name' => $data['name'],
			]);
	}

	/**
	 * Test that storing a campus rejects abbreviations longer than 10 characters.
	 */
	public function test_store_limits_abbr_to_10_characters(): void
	{
		$data = $this->data();
		$data['abbr'] = str_repeat('a', 11);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['abbr']);
		$this->assertDatabaseMissing('campuses',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

}
