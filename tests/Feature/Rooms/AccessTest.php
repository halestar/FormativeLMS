<?php

namespace Tests\Feature\Rooms;

use App\Models\Locations\Room;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function showRoute(): string
	{
		return 'locations.rooms.show';
	}

	protected function createRoute(): string
	{
		return 'locations.rooms.create';
	}

	protected function editRoute(): string
	{
		return 'locations.rooms.edit';
	}


	public function test_show_access_matrix(): void
	{
		$room = Room::inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->showRoute(),
			routeParameters: ['room' => $room->id],
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

	public function test_create_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->createRoute(),
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
		$room = Room::inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->editRoute(),
			routeParameters: ['room' => $room->id],
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net'],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}
}
