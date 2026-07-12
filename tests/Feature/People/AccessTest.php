<?php

namespace Tests\Feature\People;

use App\Models\People\Person;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function indexRoute(): string
	{
		return 'people.index';
	}

	protected function showRoute(): string
	{
		return 'people.show';
	}

	protected function editRoute(): string
	{
		return 'people.edit';
	}

	protected function changeSelfPasswordRoute(): string
	{
		return 'people.password';
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
		$person = Person::inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->showRoute(),
			routeParameters: ['person' => $person->school_id],
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
		$person = Person::whereNotIn('email', self::$serviceAccounts)->inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->editRoute(),
			routeParameters: ['person' => $person->school_id],
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net'],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_edit_self_access_matrix(): void
	{
		foreach (self::$serviceAccounts as $email)
		{
			$person = Person::where('email', $email)->first();
			$this->assertRouteAccess(
				$this->editRoute(),
				routeParameters: ['person' => $person->school_id],
				allowedEmails: [$email],
				guestStatus: Response::HTTP_OK,
			);
		}
	}

	public function test_change_self_password_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->changeSelfPasswordRoute(),
			allowedEmails: self::$serviceAccounts,
			guestStatus: Response::HTTP_OK,
		);
	}
}
