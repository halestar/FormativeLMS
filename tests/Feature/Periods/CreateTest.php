<?php

namespace Tests\Feature\Periods;

use App\Classes\Settings\Days;
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

	protected function store($id): string
	{
		return route('locations.periods.store', $id);
	}

	protected function data(): array
	{
		return [
			'name'  => 'New Period',
			'abbr'  => 'NP',
			'day'   => Days::MONDAY,
			'start' => '08:00',
			'end'   => '09:00',
		];
	}

	public function test_store_admin(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);

		$this->assertDatabaseHas('periods',
			[
				'campus_id' => $campus->id,
				'name'      => $data['name'],
				'abbr'      => $data['abbr'],
				'day'       => $data['day'],
				'start'     => $data['start'],
				'end'       => $data['end'],
				'active'    => true,
			]);
		$response->assertRedirect($this->edit($campus->id));
	}

	public function test_store_staff(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$response = $this->actingAsServiceAccount('staff@kalinec.net')
		                 ->post($this->store($campus->id), $data);

		$this->assertDatabaseHas('periods',
			[
				'campus_id' => $campus->id,
				'name'      => $data['name'],
				'abbr'      => $data['abbr'],
				'day'       => $data['day'],
				'start'     => $data['start'],
				'end'       => $data['end'],
				'active'    => true,
			]);
		$response->assertRedirect($this->edit($campus->id));
	}

	public function test_store_faculty(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$response = $this->actingAsServiceAccount('faculty@kalinec.net')
		                 ->post($this->store($campus->id), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('periods',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

	public function test_store_student(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$response = $this->actingAsServiceAccount('student@kalinec.net')
		                 ->post($this->store($campus->id), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('periods',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

	public function test_store_parent(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$response = $this->actingAsServiceAccount('parent@kalinec.net')
		                 ->post($this->store($campus->id), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('periods',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

	public function test_store_guest(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$response = $this->post($this->store($campus->id), $data);

		$response->assertRedirect(route('login'));
	}

	/**
	 * Test that storing a period requires a name.
	 */
	public function test_store_requires_name(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		unset($data['name']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);
		$response->assertRedirectBackWithErrors(['name']);
		$this->assertDatabaseMissing('periods', ['abbr' => $data['abbr']]);
	}

	/**
	 * Test that storing a period rejects names longer than 255 characters.
	 */
	public function test_store_limits_name_to_255_characters(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$data['name'] = str_repeat('a', 256);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);
		$response->assertRedirectBackWithErrors(['name']);
		$this->assertDatabaseMissing('periods',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

	/**
	 * Test that storing a period requires an abbreviation.
	 */
	public function test_store_requires_abbr(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		unset($data['abbr']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);
		$response->assertRedirectBackWithErrors(['abbr']);
		$this->assertDatabaseMissing('periods', ['name' => $data['name']]);
	}

	/**
	 * Test that storing a period rejects abbreviations longer than 10 characters.
	 */
	public function test_store_limits_abbr_to_10_characters(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$data['abbr'] = str_repeat('a', 11);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);
		$response->assertRedirectBackWithErrors(['abbr']);
		$this->assertDatabaseMissing('periods',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

	/**
	 * Test that storing a period requires a day.
	 */
	public function test_store_requires_day(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		unset($data['day']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);
		$response->assertRedirectBackWithErrors(['day']);
		$this->assertDatabaseMissing('periods',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

	/**
	 * Test that storing a period rejects non-numeric day values.
	 */
	public function test_store_rejects_non_numeric_day(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$data['day'] = "Monday";
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);
		$response->assertRedirectBackWithErrors(['day']);
		$this->assertDatabaseMissing('periods',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

	/**
	 * Test that storing a period rejects day values outside the allowed weekdays.
	 */
	public function test_store_rejects_invalid_day_value(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$data['day'] = 15;
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);
		$response->assertRedirectBackWithErrors(['day']);
		$this->assertDatabaseMissing('periods',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

	/**
	 * Test that storing a period requires a start time.
	 */
	public function test_store_requires_start(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		unset($data['start']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);
		$response->assertRedirectBackWithErrors(['start']);
		$this->assertDatabaseMissing('periods',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

	/**
	 * Test that storing a period rejects start times that do not match H:i.
	 */
	public function test_store_rejects_invalid_start_format(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$data['start'] = '08:00:00';
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);
		$response->assertRedirectBackWithErrors(['start']);
		$this->assertDatabaseMissing('periods',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

	/**
	 * Test that storing a period requires an end time.
	 */
	public function test_store_requires_end(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		unset($data['end']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);
		$response->assertRedirectBackWithErrors(['end']);
		$this->assertDatabaseMissing('periods',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

	/**
	 * Test that storing a period rejects end times that do not match H:i.
	 */
	public function test_store_rejects_invalid_end_format(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$data['end'] = '09:00:00';
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);
		$response->assertRedirectBackWithErrors(['end']);
		$this->assertDatabaseMissing('periods',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

	/**
	 * Test that storing a period rejects of the end time is before the start time
	 */
	public function test_store_rejects_end_before_start(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$data['start'] = '09:00';
		$data['end'] = '08:00';
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);
		$response->assertRedirectBackWithErrors(['end']);
		$this->assertDatabaseMissing('periods',
			[
				'name' => $data['name'],
				'abbr' => $data['abbr'],
			]);
	}

}
