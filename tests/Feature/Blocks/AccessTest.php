<?php

namespace Tests\Feature\Blocks;

use App\Models\Schedules\Block;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use DatabaseTransactions;
	use InteractsWithServiceAccounts;

	protected function editRoute(): string
	{
		return 'locations.blocks.edit';
	}

	public function test_edit_access_matrix(): void
	{
		$block = Block::inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->editRoute(),
			routeParameters: ['block' => $block->id],
			allowedEmails: ['fablms@kalinec.net'],
			deniedEmails: [
				'staff@kalinec.net',
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}
}
