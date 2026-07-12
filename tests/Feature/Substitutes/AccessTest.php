<?php

namespace Tests\Feature\Substitutes;

use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function indexRoute(): string
	{
		return 'features.substitutes.index';
	}

	protected function poolIndexRoute(): string
	{
		return 'features.substitutes.index';
	}

	public function test_index_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->indexRoute(),
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_pool_index_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->poolIndexRoute(),
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net'],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}
}
