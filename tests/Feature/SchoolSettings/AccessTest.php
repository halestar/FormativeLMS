<?php

namespace Tests\Feature\SchoolSettings;

use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function showRoute(): string
	{
		return 'settings.school';
	}

	public function test_show_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->showRoute(),
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}
}
