<?php

namespace Tests\Feature\Support;

use App\Models\People\Person;
use Symfony\Component\HttpFoundation\Response;

trait InteractsWithServiceAccounts
{
	protected static array $serviceAccounts =
		[
			'fablms@kalinec.net',
			'staff@kalinec.net',
			'faculty@kalinec.net',
			'student@kalinec.net',
			'parent@kalinec.net',
		];
	protected const DEFAULT_ACCESS_ALLOWED_STATUS = Response::HTTP_OK;
	protected const DEFAULT_ACCESS_DENIED_STATUS = Response::HTTP_FORBIDDEN;

	protected function serviceAccount(string $email): Person
	{
		return Person::where('email', $email)->firstOrFail();
	}

	protected function actingAsServiceAccount(string $email): static
	{
		return $this->actingAs($this->serviceAccount($email));
	}

	/**
	 * Assert that a route is accessible by the expected accounts and denied to everyone else.
	 *
	 * @param array<int, string> $allowedEmails
	 * @param array<int, string> $deniedEmails
	 */
	protected function assertRouteAccess(
		string $routeName,
		array  $routeParameters = [],
		array  $allowedEmails = [],
		array  $deniedEmails = [],
		?int   $guestStatus = self::DEFAULT_ACCESS_DENIED_STATUS,
	): void
	{
		$url = route($routeName, $routeParameters);

		foreach ($allowedEmails as $email)
		{
			$this->assertRouteStatusForAccount(
				email: $email,
				routeName: $routeName,
				url: $url,
				expectedStatus: self::DEFAULT_ACCESS_ALLOWED_STATUS,
			);
		}

		foreach ($deniedEmails as $email)
		{
			$this->assertRouteStatusForAccount(
				email: $email,
				routeName: $routeName,
				url: $url,
				expectedStatus: self::DEFAULT_ACCESS_DENIED_STATUS,
			);
		}

		if ($guestStatus !== null)
		{
			$this->assertGuestRouteStatus($routeName, $url, $guestStatus);
		}
	}

	protected function assertRouteStatusForAccount(
		string $email,
		string $routeName,
		string $url,
		int    $expectedStatus,
	): void
	{
		$response = $this->actingAsServiceAccount($email)
		                 ->get($url);

		$this->assertSame(
			$expectedStatus,
			$response->getStatusCode(),
			sprintf(
				'Route [%s] returned status [%d] for account [%s] at [%s], expected [%d].',
				$routeName,
				$response->getStatusCode(),
				$email,
				$url,
				$expectedStatus,
			),
		);
	}

	protected function assertGuestRouteStatus(string $routeName, string $url, int $expectedStatus): void
	{
		$response = $this->get($url);

		$this->assertSame(
			$expectedStatus,
			$response->getStatusCode(),
			sprintf(
				'Route [%s] returned status [%d] for guest at [%s], expected [%d].',
				$routeName,
				$response->getStatusCode(),
				$url,
				$expectedStatus,
			),
		);
		if ($expectedStatus == Response::HTTP_OK)
			$response->assertLocation(url('/'));
	}

	protected function assertGuestRedirectsToLogin(string $routeName, array $routeParameters = []): void
	{
		$url = route($routeName, $routeParameters);

		$this->get($url)
		     ->assertRedirect(route('login'));
	}
}
