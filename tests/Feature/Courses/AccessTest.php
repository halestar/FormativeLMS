<?php

namespace Tests\Feature\Courses;

use App\Models\SubjectMatter\Course;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class AccessTest extends TestCase
{
	use DatabaseTransactions;
	use InteractsWithServiceAccounts;

	protected function indexRoute(): string
	{
		return 'subjects.courses.index';
	}

	protected function editRoute(): string
	{
		return 'subjects.courses.edit';
	}

	public function test_index_access_matrix(): void
	{
		$this->assertRouteAccess(
			$this->indexRoute(),
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
		$course = Course::first();
		$this->assertRouteAccess(
			$this->editRoute(),
			routeParameters: ['course' => $course->id],
			allowedEmails: ['fablms@kalinec.net', 'staff@kalinec.net'],
			deniedEmails: [
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			],
		);
	}
}
