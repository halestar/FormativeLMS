<?php

namespace Tests\Feature\Campuses;

use App\Models\Locations\Campus;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function indexRoute(): string
	{
		return 'locations.campuses.index';
	}

	protected function showRoute(): string
	{
		return 'locations.campuses.show';
	}

	protected function editRoute(): string
	{
		return 'locations.campuses.edit';
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

	public function test_show_access_matrix(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->showRoute(),
			routeParameters: ['campus' => $campus->id],
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_edit_access_matrix(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->editRoute(),
			routeParameters: ['campus' => $campus->id],
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}
}
