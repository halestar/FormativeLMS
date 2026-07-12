<?php

namespace Tests\Feature\LocalIntegrator;

use App\Models\SubjectMatter\SchoolClass;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function authIndexRoute(): string
	{
		return 'integrators.local.auth.index';
	}

	protected function classesIndexRoute(): string
	{
		return 'integrators.local.classes.index';
	}

	protected function documentsIndexRoute(): string
	{
		return 'integrators.local.documents.index';
	}

	protected function workIndexRoute(): string
	{
		return 'integrators.local.work.index';
	}

	protected function aiConfigRoute(): string
	{
		return 'integrators.local.services.ai.config';
	}

	protected function aiConfigPersonalRoute(): string
	{
		return 'integrators.local.services.ai.config.personal';
	}

	protected function classesPreferencesRoute(): string
	{
		return 'integrators.local.services.classes.preferences';
	}

	public function test_auth_index_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->authIndexRoute(),
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_classes_index_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->classesIndexRoute(),
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_documents_index_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->documentsIndexRoute(),
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_work_index_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->workIndexRoute(),
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_ai_config_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->aiConfigRoute(),
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_ai_config_personal_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->aiConfigPersonalRoute(),
			allowedEmails: self::$serviceAccounts,
			guestStatus: Response::HTTP_OK,
		);
	}


	public function test_classes_preferences_access_matrix(): void
	{
		$schoolClass = SchoolClass::inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->classesPreferencesRoute(),
			routeParameters: ['schoolClass' => $schoolClass->id],
			allowedEmails: ['fablms@kalinec.net', 'faculty@kalinec.net',],
			deniedEmails: [
				'staff@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}
}
