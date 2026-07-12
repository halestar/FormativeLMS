<?php

namespace Tests\Feature\StudentTracker;

use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function indexRoute(): string
	{
		return 'subjects.student-tracker.index';
	}

	protected function editRoute(): string
	{
		return 'subjects.student-tracker.edit';
	}

	public function test_index_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->indexRoute(),
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net'],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

	public function test_edit_access_matrix(): void
	{
		$person = $this->serviceAccount('faculty@kalinec.net');
		$this->assertRouteAccess(
			$this->editRoute(),
			routeParameters: ['student_tracker' => $person->school_id],
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net'],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}

}
