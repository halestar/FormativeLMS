<?php

namespace Tests\Feature\PersonalPreferences;

use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function communicationsRoute(): string
	{
		return 'people.preferences.communications';
	}

	protected function passkeysRoute(): string
	{
		return 'people.preferences.passkeys';
	}

	public function test_communications_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->communicationsRoute(),
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

	public function test_passkeys_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->passkeysRoute(),
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
}
