<?php

namespace Tests\Feature\Permissions;

use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function indexRoute(): string
	{
		return 'settings.permissions.index';
	}

	protected function createRoute(): string
	{
		return 'settings.permissions.create';
	}

	protected function editRoute(): string
	{
		return 'settings.permissions.edit';
	}

	public function test_index_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->indexRoute(),
			allowedEmails: ['fablms@kalinec.net'],
			deniedEmails: [
				'staff@kalinec.net',
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_create_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->createRoute(),
			allowedEmails: ['fablms@kalinec.net'],
			deniedEmails: [
				'staff@kalinec.net',
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_edit_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->editRoute(),
			routeParameters: ['permission' => 1],
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
