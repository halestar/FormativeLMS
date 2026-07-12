<?php

namespace Tests\Feature\Home;

use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function indexRoute(): string
	{
		return 'home';
	}

	public function test_index_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->indexRoute(),
			allowedEmails: self::$serviceAccounts,
			guestStatus: Response::HTTP_OK,
		);
	}
}
