<?php

namespace Tests\Feature\Periods;

use App\Models\Locations\Campus;
use App\Models\Schedules\Period;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function createRoute(): string
	{
		return 'locations.periods.create';
	}

	protected function editRoute(): string
	{
		return 'locations.periods.edit';
	}

	public function test_create_access_matrix(): void
	{
		$campus = Campus::inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->createRoute(),
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
		$period = Period::inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->editRoute(),
			routeParameters: ['period' => $period->id],
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}
}
