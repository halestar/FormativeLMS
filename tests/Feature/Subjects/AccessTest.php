<?php

namespace Tests\Feature\Subjects;

use App\Models\SubjectMatter\Subject;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function indexRoute(): string
	{
		return 'subjects.subjects.index';
	}

	protected function editRoute(): string
	{
		return 'subjects.subjects.edit';
	}

	public function test_index_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->indexRoute(),
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
		$subject = Subject::inRandomOrder()->first();
		$this->assertRouteAccess(
			$this->editRoute(),
			routeParameters: ['subject' => $subject->id],
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net'],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}
}
