<?php

namespace Tests\Feature\Skills;

use App\Models\SubjectMatter\Assessment\Skill;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use DatabaseTransactions;
	use InteractsWithServiceAccounts;

	protected function showRoute(): string
	{
		return 'subjects.skills.show';
	}

	protected function createRoute(): string
	{
		return 'subjects.skills.create';
	}

	protected function editRoute(): string
	{
		return 'subjects.skills.edit';
	}

	public function test_show_access_matrix(): void
	{
		$skill = Skill::inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->showRoute(),
			routeParameters: ['skill' => $skill->id],
			allowedEmails: [
				'fablms@kalinec.net',
				'staff@kalinec.net',
			],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_create_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->createRoute(),
			allowedEmails: [
				'fablms@kalinec.net',
				'staff@kalinec.net',
			],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_edit_access_matrix(): void
	{
		$skill = Skill::inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->editRoute(),
			routeParameters: ['skill' => $skill->id],
			allowedEmails: [
				'fablms@kalinec.net',
				'staff@kalinec.net',
			],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}
}
