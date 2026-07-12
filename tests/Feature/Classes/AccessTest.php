<?php

namespace Tests\Feature\Classes;

use App\Models\People\Person;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function criteriaRoute(): string
	{
		return 'learning.classes.criteria';
	}

	protected function settingsRoute(): string
	{
		return 'learning.classes.settings';
	}

	public function test_show_own_class_access_matrix(): void
	{
		$session =
			Person::where('email', 'faculty@kalinec.net')->first()->classesTaught->first();
		$this->assertRouteAccess(
			$this->criteriaRoute(),
			routeParameters: ['classSession' => $session->id],
			allowedEmails: ['fablms@kalinec.net', 'faculty@kalinec.net'],
			deniedEmails: [
				'staff@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_own_class_settings_access_matrix(): void
	{
		$session =
			Person::where('email', 'faculty@kalinec.net')->first()->classesTaught->first();
		$this->assertRouteAccess(
			$this->settingsRoute(),
			routeParameters: ['classSession' => $session->id],
			allowedEmails: ['fablms@kalinec.net', 'faculty@kalinec.net'],
			deniedEmails: [
				'staff@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}


}
