<?php

namespace Tests\Feature\Id;

use App\Models\Locations\Campus;
use App\Models\Utilities\SchoolRoles;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function showRoute(): string
	{
		return 'people.school-ids.show';
	}

	protected function manageGlobalRoute(): string
	{
		return 'people.school-ids.manage.global';
	}

	protected function manageCampusRoute(): string
	{
		return 'people.school-ids.manage.campus';
	}

	protected function manageRoleRoute(): string
	{
		return 'people.school-ids.manage.role';
	}

	protected function manageRoleCampusRoute(): string
	{
		return 'people.school-ids.manage.both';
	}

	public function test_show_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->showRoute(),
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

	public function test_manage_global_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->manageGlobalRoute(),
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_manage_campus_access_matrix(): void
	{
		$campus = Campus::first();
		$this->assertRouteAccess(
			$this->manageCampusRoute(),
			routeParameters: ['campus' => $campus->id],
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_manage_role_access_matrix(): void
	{
		$role = SchoolRoles::StudentRole();
		$this->assertRouteAccess(
			$this->manageRoleRoute(),
			routeParameters: ['role' => $role->id],
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_manage_role_campus_access_matrix(): void
	{
		$role = SchoolRoles::StudentRole();
		$campus = Campus::first();
		$this->assertRouteAccess(
			$this->manageRoleRoute(),
			routeParameters: ['role' => $role->id, 'campus' => $campus->id],
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net',],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}
}
