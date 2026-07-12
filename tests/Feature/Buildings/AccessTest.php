<?php

namespace Tests\Feature\Buildings;

use App\Models\Locations\Building;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function indexRoute(): string
	{
		return 'locations.buildings.index';
	}

	protected function showRoute(): string
	{
		return 'locations.buildings.show';
	}

	protected function editRoute(): string
	{
		return 'locations.buildings.edit';
	}

	public function test_index_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->indexRoute(),
			allowedEmails: [
				'fablms@kalinec.net',
				'staff@kalinec.net',
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
			guestStatus: Response::HTTP_OK,
		);
	}

	public function test_show_access_matrix(): void
	{
		$building = Building::inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->showRoute(),
			routeParameters: ['building' => $building->id],
			allowedEmails: [
				'fablms@kalinec.net',
				'staff@kalinec.net',
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
			guestStatus: Response::HTTP_OK,
		);
	}

	public function test_edit_access_matrix(): void
	{
		$building = Building::inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->editRoute(),
			routeParameters: ['building' => $building->id],
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}
}
