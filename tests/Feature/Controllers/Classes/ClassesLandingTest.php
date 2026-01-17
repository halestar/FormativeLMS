<?php

namespace Feature\Controllers\Classes;

use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ClassesLandingTest extends TestCase
{
	use DatabaseTransactions;

	protected function index(): string
	{
		return route('subjects.classes.index');
	}

	protected function show(ClassSession $class): string
	{
		return route('subjects.school.classes.show', $class);
	}

	/**
	 * index
	 */
	public function test_landing_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->index());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_landing_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
        $response = $this->actingAs($staff)
	        ->get($this->index());
        $response->assertStatus(Response::HTTP_OK);
    }

	public function test_landing_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->index());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_landing_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->index());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_landing_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->index());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_landing_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->index());
		$response->assertRedirect(route('login'));
	}

	/**
	 * show
	 */
	public function test_show_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$class = ClassSession::inRandomOrder()->first();
		$response = $this->actingAs($admin)
			->get($this->show($class));
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_show_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$class = $staff->studentTrackee()->first()->classSessions()->inRandomOrder()->first();
		$response = $this->actingAs($staff)
			->get($this->show($class));
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_show_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$class = $faculty->currentClassSessions()->inRandomOrder()->first();
		$response = $this->actingAs($faculty)
			->get($this->show($class));
		$response->assertStatus(Response::HTTP_FOUND);
	}

	public function test_show_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$class = $student->student()->classSessions()->inRandomOrder()->first();
		$response = $this->actingAs($student)
			->get($this->show($class));
		$response->assertStatus(Response::HTTP_OK);
	}

	/**public function test_show_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();

		$class = $parent->currentChildStudents()->first()->classSessions()->inRandomOrder()->first();
		$response = $this->actingAs($parent)
			->get($this->show($class));
		$response->assertStatus(Response::HTTP_OK);
	}**/

	public function test_show_guest(): void
	{
		$class = ClassSession::inRandomOrder()->first();
		$response = $this->actingAsGuest()
			->get($this->show($class));
		$response->assertRedirect(route('login'));
	}
}
