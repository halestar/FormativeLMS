<?php

namespace Tests\Feature\Blocks;

use App\Models\Locations\Campus;
use App\Models\Schedules\Block;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class CreateTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function edit($id): string
	{
		return route('locations.blocks.edit', $id);
	}

	protected function store($id): string
	{
		return route('locations.blocks.store', $id);
	}

	protected function data(): array
	{
		return [
			'block_name' => 'Test Block',
		];
	}

	public function test_store_admin(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);

		$this->assertDatabaseHas('blocks',
			[
				'name'      => $data['block_name'],
				'campus_id' => $campus->id,
				'active'    => true,
				'order'     => 0,
			]);
		$newBlock = Block::where('name', $data['block_name'])->first();
		$response->assertRedirect($this->edit($newBlock->id));
	}

	public function test_store_staff(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$response = $this->actingAsServiceAccount('staff@kalinec.net')
		                 ->post($this->store($campus->id), $data);

		$this->assertDatabaseHas('blocks',
			[
				'name'      => $data['block_name'],
				'campus_id' => $campus->id,
				'active'    => true,
				'order'     => 0,
			]);
		$newBlock = Block::where('name', $data['block_name'])->first();
		$response->assertRedirect($this->edit($newBlock->id));
	}

	public function test_store_faculty(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$response = $this->actingAsServiceAccount('faculty@kalinec.net')
		                 ->post($this->store($campus->id), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('blocks', ['name' => $data['block_name']]);
	}

	public function test_store_student(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$response = $this->actingAsServiceAccount('student@kalinec.net')
		                 ->post($this->store($campus->id), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('blocks', ['name' => $data['block_name']]);
	}

	public function test_store_parent(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$response = $this->actingAsServiceAccount('parent@kalinec.net')
		                 ->post($this->store($campus->id), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('blocks', ['name' => $data['block_name']]);
	}

	public function test_store_guest(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = $this->data();
		$response = $this->post($this->store($campus->id), $data);
		$response->assertRedirect(route('login'));
	}


	/**
	 * Test that storing a block requires a block name.
	 */
	public function test_store_requires_block_name(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), []);

		$response->assertRedirectBackWithErrors(['block_name']);
	}

	/**
	 * Test that storing a block rejects names longer than twenty characters.
	 */
	public function test_store_limits_block_name_to_twenty_characters(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$data = ['name' => str_repeat('a', 21)];
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store($campus->id), $data);

		$response->assertRedirectBackWithErrors(['block_name']);
	}
}
